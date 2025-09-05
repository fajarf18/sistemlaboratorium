<?php

namespace App\Mail;

use App\Models\Peminjaman; // Import model Peminjaman
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ItemReturnNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $peminjaman;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Notifikasi Pengembalian Barang #' . $this->peminjaman->id)
                    ->view('emails.item_return');
    }
}