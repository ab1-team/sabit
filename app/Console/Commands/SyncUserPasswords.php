<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SyncUserPasswords extends Command
{
    protected $signature = 'users:sync-passwords';
    protected $description = 'Set each user password = email (chunked)';

    public function handle()
    {
        $count = 0;
        DB::table('users')->orderBy('id')->chunkById(200, function ($users) use (&$count) {
            foreach ($users as $u) {
                if (empty($u->email)) continue;
                DB::table('users')->where('id', $u->id)->update([
                    'password' => Hash::make($u->email),
                ]);
                $count++;
                $this->line($u->email . ' => updated');
            }
        });
        $this->info("Done. {$count} users updated.");
        return 0;
    }
}
