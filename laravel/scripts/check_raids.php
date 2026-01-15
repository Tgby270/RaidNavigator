<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ADHERER;
use App\Models\RAIDS;
use Illuminate\Support\Facades\DB;

$userId = 1;
$clubId = ADHERER::getClubByUserId($userId);
echo "ADHERER::getClubByUserId($userId) => "; var_export($clubId); echo PHP_EOL;
if ($clubId) {
    $count = RAIDS::getRaidNumberByClub($clubId);
    echo "RAIDS::getRaidNumberByClub($clubId) => $count" . PHP_EOL;
    $rows = DB::table('sae_raids')->where('CLU_ID', $clubId)->get();
    echo "Rows for club $clubId:\n";
    foreach ($rows as $r) {
        echo "RAID_ID={$r->RAID_ID}, raid_nom={$r->raid_nom}\n";
    }
} else {
    echo "No club for user $userId\n";
}
