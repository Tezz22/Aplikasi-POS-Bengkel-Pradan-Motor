<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Pelanggan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
}
