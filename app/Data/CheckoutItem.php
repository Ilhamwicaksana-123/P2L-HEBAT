<?php

namespace App\Data;

use App\Models\KeranjangItem;
use App\Models\Produk;

class CheckoutItem
{
    public function __construct(
        public Produk $produk,
        public int $qty,
        public float $harga_satuan,
        public float $subtotal,
        public ?KeranjangItem $cartItem = null,
    ) {
    }

    public static function fromCartItem(KeranjangItem $cartItem): self
    {
        return new self(
            produk: $cartItem->produk,
            qty: (int) $cartItem->qty,
            harga_satuan: (float) $cartItem->harga_satuan,
            subtotal: (float) $cartItem->subtotal,
            cartItem: $cartItem,
        );
    }

    public static function fromProduct(Produk $product, int $qty): self
    {
        $hargaSatuan = (float) $product->harga_produk;

        return new self(
            produk: $product,
            qty: $qty,
            harga_satuan: $hargaSatuan,
            subtotal: $qty * $hargaSatuan,
        );
    }
}
