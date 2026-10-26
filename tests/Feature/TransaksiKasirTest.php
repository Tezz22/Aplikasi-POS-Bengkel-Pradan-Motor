<?php

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('adds selected goods to the cashier cart and increases their quantity', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 3,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 2,
        ],
    ];

    $this->actingAs($kasir)
        ->from(route('kasir.transaksi'))
        ->post(route('kasir.transaksi.cart.add'), [
            'type' => 'barang',
            'id' => $barang->id_barang,
        ])
        ->assertRedirect(route('kasir.transaksi'));

    $this->from(route('kasir.transaksi'))
        ->post(route('kasir.transaksi.cart.add'), [
            'type' => 'barang',
            'id' => $barang->id_barang,
        ])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('status', 'Item berhasil ditambahkan ke keranjang.')
        ->assertSessionHas('kasir_cart', $cart);
});

it('adds jasa to the cashier cart', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $jasa = Jasa::create([
        'nama_jasa' => 'Servis Ringan',
        'tarif' => 50000,
    ]);

    $this->actingAs($kasir)
        ->from(route('kasir.transaksi'))
        ->post(route('kasir.transaksi.cart.add'), [
            'type' => 'jasa',
            'id' => $jasa->id_jasa,
        ])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('kasir_cart', [
            "jasa:{$jasa->id_jasa}" => [
                'type' => 'jasa',
                'id' => $jasa->id_jasa,
                'quantity' => 1,
            ],
        ]);
});

it('does not add goods when the cart quantity reaches available stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 1,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 1,
        ],
    ];

    $this->actingAs($kasir)
        ->withSession(['kasir_cart' => $cart])
        ->from(route('kasir.transaksi'))
        ->post(route('kasir.transaksi.cart.add'), [
            'type' => 'barang',
            'id' => $barang->id_barang,
        ])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('error', 'Stok barang tidak mencukupi untuk ditambahkan.')
        ->assertSessionHas('kasir_cart', $cart);
});

it('increases and decreases cart quantities without going below one', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 3,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 2,
        ],
    ];
    $url = route('kasir.transaksi.cart.quantity', [
        'type' => 'barang',
        'id' => $barang->id_barang,
    ]);

    $this->actingAs($kasir)
        ->withSession(['kasir_cart' => $cart])
        ->from(route('kasir.transaksi'))
        ->patch($url, ['action' => 'decrease'])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('kasir_cart', [
            "barang:{$barang->id_barang}" => [
                'type' => 'barang',
                'id' => $barang->id_barang,
                'quantity' => 1,
            ],
        ]);

    $this->from(route('kasir.transaksi'))
        ->patch($url, ['action' => 'decrease'])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('error', 'Jumlah item minimal satu. Gunakan ikon tempat sampah untuk menghapus item.')
        ->assertSessionHas('kasir_cart', [
            "barang:{$barang->id_barang}" => [
                'type' => 'barang',
                'id' => $barang->id_barang,
                'quantity' => 1,
            ],
        ]);

    $this->from(route('kasir.transaksi'))
        ->patch($url, ['action' => 'increase'])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('kasir_cart', $cart);
});

it('does not increase a cart item beyond the available stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 1,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 1,
        ],
    ];

    $this->actingAs($kasir)
        ->withSession(['kasir_cart' => $cart])
        ->from(route('kasir.transaksi'))
        ->patch(route('kasir.transaksi.cart.quantity', [
            'type' => 'barang',
            'id' => $barang->id_barang,
        ]), ['action' => 'increase'])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('error', 'Stok barang tidak mencukupi untuk ditambahkan.')
        ->assertSessionHas('kasir_cart', $cart);
});

it('processes a valid payment, records its receipt, and updates stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $pelanggan = Pelanggan::create(['nama' => 'Budi']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 3,
    ]);
    $jasa = Jasa::create([
        'nama_jasa' => 'Servis Ringan',
        'tarif' => 50000,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 2,
        ],
        "jasa:{$jasa->id_jasa}" => [
            'type' => 'jasa',
            'id' => $jasa->id_jasa,
            'quantity' => 1,
        ],
    ];

    $response = $this->actingAs($kasir)
        ->withSession(['kasir_cart' => $cart])
        ->post(route('kasir.transaksi.checkout'), [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nominal_bayar' => 120000,
        ]);

    $penjualan = Penjualan::query()->sole();
    $response->assertRedirect(route('kasir.transaksi.receipt', ['penjualan' => $penjualan, 'print' => 1]))
        ->assertSessionHas('status', 'Pembayaran berhasil. Silakan cetak struk transaksi.')
        ->assertSessionMissing('kasir_cart');
    $this->assertDatabaseHas('tr_penjualan', [
        'id_user' => $kasir->id_user,
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'total' => 100000,
        'nominal_bayar' => 120000,
        'kembalian' => 20000,
    ]);
    $this->assertDatabaseHas('detail_penjualan', [
        'id_transaksi' => $penjualan->id_transaksi,
        'id_barang' => $barang->id_barang,
        'nama_item' => 'Oli Mesin',
        'qty' => 2,
        'subtotal' => 50000,
    ]);
    $this->assertDatabaseHas('detail_penjualan', [
        'id_transaksi' => $penjualan->id_transaksi,
        'id_jasa' => $jasa->id_jasa,
        'nama_item' => 'Servis Ringan',
        'qty' => 1,
        'subtotal' => 50000,
    ]);
    expect($barang->fresh()->stok)->toBe(1);

    $this->get(route('kasir.transaksi.receipt', $penjualan))
        ->assertOk()
        ->assertSee($penjualan->no_transaksi)
        ->assertSee('Rp 20.000')
        ->assertSee('Cetak Struk')
        ->assertDontSee("window.addEventListener('load', () => window.print());", false);

    $this->get(route('kasir.transaksi.receipt', ['penjualan' => $penjualan, 'print' => 1]))
        ->assertOk()
        ->assertSee("window.addEventListener('load', () => window.print());", false);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->get(route('kasir.transaksi.receipt', $penjualan))
        ->assertForbidden();
});

it('rejects insufficient payment without saving the sale or reducing stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 3,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 2,
        ],
    ];

    $this->actingAs($kasir)
        ->withSession(['kasir_cart' => $cart])
        ->from(route('kasir.transaksi'))
        ->post(route('kasir.transaksi.checkout'), [
            'nominal_bayar' => 49999,
            '_payment_modal' => 1,
        ])
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHasErrors('nominal_bayar')
        ->assertSessionHas('kasir_cart', $cart);

    $this->assertDatabaseCount('tr_penjualan', 0);
    expect($barang->fresh()->stok)->toBe(3);
});

it('renders cart quantities and totals and removes a selected item', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
    $barang = Barang::create([
        'id_kategori' => $kategori->id_kategori,
        'nama_barang' => 'Oli Mesin',
        'satuan' => 'botol',
        'harga_jual' => 25000,
        'stok' => 3,
    ]);
    $jasa = Jasa::create([
        'nama_jasa' => 'Servis Ringan',
        'tarif' => 50000,
    ]);
    $cart = [
        "barang:{$barang->id_barang}" => [
            'type' => 'barang',
            'id' => $barang->id_barang,
            'quantity' => 2,
        ],
        "jasa:{$jasa->id_jasa}" => [
            'type' => 'jasa',
            'id' => $jasa->id_jasa,
            'quantity' => 1,
        ],
    ];

    $this->actingAs($kasir)
        ->withSession(['kasir_cart' => $cart])
        ->get(route('kasir.transaksi'))
        ->assertOk()
        ->assertSee('Oli Mesin')
        ->assertSee('Servis Ringan')
        ->assertSee('Rp 100.000')
        ->assertSee('3 Item')
        ->assertSee('Kurangi Oli Mesin')
        ->assertSee('Tambah Oli Mesin')
        ->assertSee('Hapus Oli Mesin')
        ->assertSee('Proses Pembayaran &amp; Cetak Struk', false)
        ->assertSee('Uang Tunai Dibayarkan (Rp)')
        ->assertSee('id="nominal-bayar-display"', false)
        ->assertSee('name="nominal_bayar"', false)
        ->assertSee('Kembalian (Rp)');

    $this->from(route('kasir.transaksi'))
        ->delete(route('kasir.transaksi.cart.remove', [
            'type' => 'barang',
            'id' => $barang->id_barang,
        ]))
        ->assertRedirect(route('kasir.transaksi'))
        ->assertSessionHas('kasir_cart', [
            "jasa:{$jasa->id_jasa}" => $cart["jasa:{$jasa->id_jasa}"],
        ]);
});
