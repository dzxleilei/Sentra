$q = \App\Models\Borrow::where("status", "Booking")
    ->whereNull("waktu_checkin")
    ->whereRaw("waktu_mulai_booking <= NOW()");
echo "COUNT=".$q->count().PHP_EOL;
$rows = (clone $q)->orderByDesc("updated_at")->limit(10)->get(["id","kode_booking","user_id","status","waktu_mulai_booking","waktu_checkin","waktu_checkout","updated_at"]);
foreach ($rows as $row) {
    echo json_encode($row->only(["id","kode_booking","user_id","status","waktu_mulai_booking","waktu_checkin","waktu_checkout","updated_at"])).PHP_EOL;
}
