<?php

namespace App\Services;

use App\Models\Borrow;
use App\Models\Room;
use App\Models\Thing;
use Illuminate\Support\Carbon;

class BorrowLifecycleService
{
    public function enforceRules(?int $userId = null): void
    {
        $now = Carbon::now();

        $bookingQuery = Borrow::with(['room.things', 'thing', 'user'])
            ->where('status', 'Booking')
            ->whereNull('waktu_checkin');

        if ($userId !== null) {
            $bookingQuery->where('user_id', $userId);
        }

        $noCheckInBorrows = $bookingQuery
            ->where('waktu_mulai_booking', '<=', $now->copy()->subMinutes(15))
            ->get();

        foreach ($noCheckInBorrows as $borrow) {
            $borrow->update([
                'status' => 'Selesai',
                'status_checkin' => 'Tidak Check-in',
                'status_checkout' => 'Tidak Check-out',
                'catatan_pelanggaran' => 'Tidak melakukan check-in hingga 15 menit setelah jadwal mulai.',
                'penalty_points_applied' => max((int) $borrow->penalty_points_applied, 1),
            ]);

            if ($borrow->user) {
                $borrow->user->increment('penalty_points', 1);
            }

            $this->releaseResources($borrow);
        }

        $runningQuery = Borrow::with(['room.things', 'thing', 'user'])
            ->where('status', 'Berlangsung')
            ->whereNull('waktu_checkout');

        if ($userId !== null) {
            $runningQuery->where('user_id', $userId);
        }

        $noCheckoutBorrows = $runningQuery
            ->where('waktu_selesai_booking', '<=', $now->copy()->subMinutes(15))
            ->get();

        foreach ($noCheckoutBorrows as $borrow) {
            $borrow->update([
                'status' => 'Selesai',
                'status_checkout' => 'Tidak Check-out',
                'catatan_pelanggaran' => 'Tidak melakukan check-out hingga 15 menit setelah jadwal selesai.',
                'penalty_points_applied' => max((int) $borrow->penalty_points_applied, 2),
            ]);

            if ($borrow->user) {
                $borrow->user->increment('penalty_points', 2);
            }

            $this->releaseResources($borrow);
        }

        $this->syncAssetStatuses();
    }

    public function syncAssetStatuses(): void
    {
        $activeRoomIds = Borrow::where('tipe', 'Ruangan')
            ->where('status', 'Berlangsung')
            ->whereNotNull('room_id')
            ->pluck('room_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $activeRoomMap = array_fill_keys($activeRoomIds, true);

        foreach (Room::query()->get() as $room) {
            if (in_array($room->status, ['Maintenance', 'Tidak Tersedia'], true)) {
                continue;
            }

            $nextStatus = isset($activeRoomMap[(int) $room->id]) ? 'Dipakai' : 'Tersedia';
            if ($room->status !== $nextStatus) {
                $room->update(['status' => $nextStatus]);
            }
        }

        $activeThingIds = Borrow::whereNotNull('thing_id')
            ->where('status', 'Berlangsung')
            ->pluck('thing_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $activeThingMap = array_fill_keys($activeThingIds, true);

        foreach (Thing::query()->get() as $thing) {
            if ($thing->status === 'Tidak Tersedia') {
                continue;
            }

            $inActiveRoom = $thing->room_id !== null && isset($activeRoomMap[(int) $thing->room_id]);
            $isBorrowedDirectly = isset($activeThingMap[(int) $thing->id]);

            $nextStatus = $inActiveRoom
                ? 'Dipinjam'
                : ($isBorrowedDirectly ? 'Dipinjam' : 'Tersedia');

            if ($thing->status !== $nextStatus) {
                $thing->update(['status' => $nextStatus]);
            }
        }
    }

    private function releaseResources(Borrow $borrow): void
    {
        if ($borrow->tipe === 'Ruangan' && $borrow->room) {
            $borrow->room->update(['status' => 'Tersedia']);
            foreach ($borrow->room->things as $roomThing) {
                $this->refreshThingAvailability($roomThing);
            }

            return;
        }

        if ($borrow->thing) {
            $this->refreshThingAvailability($borrow->thing);
        }
    }

    private function refreshThingAvailability(Thing $thing): void
    {
        if ($thing->status === 'Tidak Tersedia') {
            return;
        }

        $isStillBorrowed = Borrow::where('thing_id', $thing->id)
            ->where('status', 'Berlangsung')
            ->exists();

        $thing->update(['status' => $isStillBorrowed ? 'Dipinjam' : 'Tersedia']);
    }
}
