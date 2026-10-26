<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    private const CART_SESSION_KEY = 'kasir_cart';

    public function index(Request $request): View
    {
        $barangs = Barang::with('kategori')->orderBy('nama_barang')->get();
        $jasas = Jasa::orderBy('nama_jasa')->get();
        $pelanggans = Pelanggan::orderBy('nama')->get();
        $cart = $request->session()->get(self::CART_SESSION_KEY, []);
        $cartBarangIds = collect($cart)->where('type', 'barang')->pluck('id');
        $cartJasaIds = collect($cart)->where('type', 'jasa')->pluck('id');
        $barangById = Barang::whereIn('id_barang', $cartBarangIds)->get()->keyBy('id_barang');
        $jasaById = Jasa::whereIn('id_jasa', $cartJasaIds)->get()->keyBy('id_jasa');
        $cartItems = [];
        $validCart = [];

        foreach ($cart as $key => $item) {
            $product = $item['type'] === 'barang'
                ? $barangById->get($item['id'])
                : $jasaById->get($item['id']);

            if ($product === null) {
                continue;
            }

            $validCart[$key] = $item;
            $cartItems[] = [
                'key' => $key,
                'type' => $item['type'],
                'id' => $item['id'],
                'name' => $item['type'] === 'barang' ? $product->nama_barang : $product->nama_jasa,
                'price' => $item['type'] === 'barang' ? $product->harga_jual : $product->tarif,
                'quantity' => $item['quantity'],
                'stock' => $item['type'] === 'barang' ? $product->stok : null,
            ];
        }

        if (count($validCart) !== count($cart)) {
            $request->session()->put(self::CART_SESSION_KEY, $validCart);
            $request->session()->flash('warning', 'Barang atau jasa yang sudah tidak tersedia dihapus dari keranjang.');
        }

        $subtotalBarang = collect($cartItems)
            ->where('type', 'barang')
            ->sum(fn (array $item): int => $item['price'] * $item['quantity']);
        $subtotalJasa = collect($cartItems)
            ->where('type', 'jasa')
            ->sum(fn (array $item): int => $item['price'] * $item['quantity']);
        $totalBayar = $subtotalBarang + $subtotalJasa;
        $itemCount = collect($cartItems)->sum('quantity');

        return view('pages.kasir.transaksi', compact(
            'barangs',
            'jasas',
            'pelanggans',
            'cartItems',
            'subtotalBarang',
            'subtotalJasa',
            'totalBayar',
            'itemCount'
        ));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['barang', 'jasa'])],
            'id' => ['required', 'integer', 'min:1'],
        ]);
        $cart = $request->session()->get(self::CART_SESSION_KEY, []);
        $key = "{$validated['type']}:{$validated['id']}";

        if ($validated['type'] === 'barang') {
            $barang = Barang::findOrFail($validated['id']);
            $quantity = $cart[$key]['quantity'] ?? 0;

            if ($quantity >= $barang->stok) {
                return back()->with('error', 'Stok barang tidak mencukupi untuk ditambahkan.');
            }
        } else {
            Jasa::findOrFail($validated['id']);
            $quantity = $cart[$key]['quantity'] ?? 0;
        }

        $cart[$key] = [
            'type' => $validated['type'],
            'id' => (int) $validated['id'],
            'quantity' => $quantity + 1,
        ];

        $request->session()->put(self::CART_SESSION_KEY, $cart);

        return back()->with('status', 'Item berhasil ditambahkan ke keranjang.');
    }

    public function removeFromCart(Request $request, string $type, int $id): RedirectResponse
    {
        $cart = $request->session()->get(self::CART_SESSION_KEY, []);
        unset($cart["{$type}:{$id}"]);
        $request->session()->put(self::CART_SESSION_KEY, $cart);

        return back()->with('status', 'Item berhasil dihapus dari keranjang.');
    }

    public function updateCartQuantity(Request $request, string $type, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['increase', 'decrease'])],
        ]);
        $cart = $request->session()->get(self::CART_SESSION_KEY, []);
        $key = "{$type}:{$id}";

        if (! isset($cart[$key])) {
            return back()->with('error', 'Item tidak ditemukan di keranjang.');
        }

        if ($type === 'barang') {
            $barang = Barang::findOrFail($id);

            if ($validated['action'] === 'increase' && $cart[$key]['quantity'] >= $barang->stok) {
                return back()->with('error', 'Stok barang tidak mencukupi untuk ditambahkan.');
            }
        } else {
            Jasa::findOrFail($id);
        }

        if ($validated['action'] === 'increase') {
            $cart[$key]['quantity']++;
        } elseif ($cart[$key]['quantity'] <= 1) {
            return back()->with('error', 'Jumlah item minimal satu. Gunakan ikon tempat sampah untuk menghapus item.');
        } else {
            $cart[$key]['quantity']--;
        }

        $request->session()->put(self::CART_SESSION_KEY, $cart);

        return back()->with('status', 'Jumlah item berhasil diperbarui.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => ['nullable', 'integer', 'exists:ms_pelanggan,id_pelanggan'],
            'nominal_bayar' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ], [
            'nominal_bayar.required' => 'Nominal uang tunai wajib diisi.',
            'nominal_bayar.integer' => 'Nominal uang tunai harus berupa angka bulat.',
            'nominal_bayar.min' => 'Nominal uang tunai tidak boleh negatif.',
            'nominal_bayar.max' => 'Nominal uang tunai terlalu besar.',
        ]);
        $cart = $request->session()->get(self::CART_SESSION_KEY, []);

        if ($cart === []) {
            return back()->with('error', 'Keranjang masih kosong. Tambahkan barang atau jasa sebelum memproses pembayaran.');
        }

        ksort($cart);

        $penjualan = DB::transaction(function () use ($request, $validated, $cart): Penjualan {
            $detailItems = [];
            $total = 0;

            foreach ($cart as $item) {
                if (
                    ! is_array($item)
                    || ! isset($item['type'], $item['id'], $item['quantity'])
                    || ! in_array($item['type'], ['barang', 'jasa'], true)
                    || ! is_numeric($item['id'])
                    || ! is_numeric($item['quantity'])
                    || (int) $item['id'] < 1
                    || (int) $item['quantity'] < 1
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'Data keranjang tidak valid. Silakan pilih ulang barang atau jasa.',
                    ]);
                }

                $quantity = (int) $item['quantity'];

                if ($item['type'] === 'barang') {
                    $product = Barang::where('id_barang', (int) $item['id'])->lockForUpdate()->firstOrFail();

                    if ($quantity > $product->stok) {
                        throw ValidationException::withMessages([
                            'cart' => "Stok {$product->nama_barang} tidak mencukupi untuk pembayaran.",
                        ]);
                    }

                    $detailItems[] = [
                        'product' => $product,
                        'type' => 'barang',
                        'id_barang' => $product->id_barang,
                        'id_jasa' => null,
                        'name' => $product->nama_barang,
                        'price' => $product->harga_jual,
                        'quantity' => $quantity,
                    ];
                } else {
                    $product = Jasa::where('id_jasa', (int) $item['id'])->lockForUpdate()->firstOrFail();

                    $detailItems[] = [
                        'product' => $product,
                        'type' => 'jasa',
                        'id_barang' => null,
                        'id_jasa' => $product->id_jasa,
                        'name' => $product->nama_jasa,
                        'price' => $product->tarif,
                        'quantity' => $quantity,
                    ];
                }

                $total += $detailItems[array_key_last($detailItems)]['price'] * $quantity;
            }

            if ($validated['nominal_bayar'] < $total) {
                throw ValidationException::withMessages([
                    'nominal_bayar' => 'Uang tunai yang dibayarkan kurang dari total pembayaran.',
                ]);
            }

            if (! empty($validated['id_pelanggan'])) {
                Pelanggan::where('id_pelanggan', $validated['id_pelanggan'])->lockForUpdate()->firstOrFail();
            }

            $penjualan = Penjualan::create([
                'id_user' => $request->user()->id_user,
                'id_pelanggan' => $validated['id_pelanggan'] ?? null,
                'no_transaksi' => 'TRX-'.now()->format('ymdHis').'-'.Str::upper(Str::random(6)),
                'tanggal' => now(),
                'total' => $total,
                'nominal_bayar' => $validated['nominal_bayar'],
                'kembalian' => $validated['nominal_bayar'] - $total,
            ]);

            foreach ($detailItems as $item) {
                PenjualanDetail::create([
                    'id_transaksi' => $penjualan->id_transaksi,
                    'id_barang' => $item['id_barang'],
                    'id_jasa' => $item['id_jasa'],
                    'jenis_item' => $item['type'],
                    'nama_item' => Str::limit($item['name'], 100, ''),
                    'harga_satuan' => $item['price'],
                    'qty' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                if ($item['type'] === 'barang') {
                    $item['product']->decrement('stok', $item['quantity']);
                }
            }

            return $penjualan;
        });

        $request->session()->forget(self::CART_SESSION_KEY);

        return redirect()
            ->route('kasir.transaksi.receipt', ['penjualan' => $penjualan, 'print' => 1])
            ->with('status', 'Pembayaran berhasil. Silakan cetak struk transaksi.');
    }

    public function receipt(Request $request, Penjualan $penjualan): View
    {
        if (
            $request->user()->role !== 'admin'
            && (int) $penjualan->id_user !== (int) $request->user()->id_user
        ) {
            throw new AuthorizationException;
        }

        $penjualan->load(['details', 'pelanggan', 'user']);
        $autoPrint = $request->boolean('print');

        return view('pages.kasir.struk', compact('penjualan', 'autoPrint'));
    }
}
