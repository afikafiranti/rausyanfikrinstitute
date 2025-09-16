<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AccountStatusNotification extends Notification
{
    public function __construct(
        public string $kind,     // approved | rejected | pending | level-up
        public array $meta = []  // {level_id, level_name, reason, note, by}
    ) {}

    public function via($notifiable): array {
        return ['database']; // in-app dulu (email opsional nanti)
    }

    public function toDatabase($notifiable): array {
        $title = match ($this->kind) {
            'approved' => 'Akun disetujui',
            'rejected' => 'Akun ditolak',
            'pending'  => 'Perubahan profil menunggu verifikasi',
            'level-up' => 'Level diperbarui',
            default    => 'Notifikasi Akun',
        };

        $message = match ($this->kind) {
            'approved' => 'Selamat! Akun Anda aktif. Level: '.($this->meta['level_name'] ?? '—'),
            'rejected' => 'Pengajuan ditolak. Alasan: '.($this->meta['reason'] ?? '—'),
            'pending'  => 'Perubahan profil membutuhkan verifikasi Koorda.',
            'level-up' => 'Level Anda kini: '.($this->meta['level_name'] ?? '—'),
            default    => ($this->meta['message'] ?? ''),
        };

        return [
            'title'      => $title,
            'message'    => $message,
            'kind'       => $this->kind,
            'meta'       => $this->meta,
            'action_url' => url('/profile'),
        ];
    }
}
