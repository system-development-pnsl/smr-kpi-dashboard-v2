<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $owner   = User::where('email', 'owner@smrhotel.com')->first();
        $gm      = User::where('email', 'gm@sunmoon.hotel')->first();
        $finance = User::where('email', 'finance@smrhotel.com')->first();
        $fo      = User::where('email', 'fo.manager@smrhotel.com')->first();
        $hk      = User::where('email', 'hk.manager@smrhotel.com')->first();

        if ($gm) {
            $gm->notify(new SystemNotification(
                'Monthly KPI report is ready for review.',
                'April 2025 KPI data has been compiled and is awaiting your approval.',
            ));

            $gm->notify(new SystemNotification(
                'Task "Staff Training Schedule Q2" is overdue.',
                'This task was due on 25 Apr 2025 and has not been marked as complete.',
            ));

            // Mark one as already read
            $read = $gm->notify(new SystemNotification(
                'New document uploaded by Finance team.',
                'Q1 Financial Summary.pdf has been processed by AI extraction.',
            ));
            $gm->notifications()->latest()->skip(0)->take(1)->update(['read_at' => now()->subHours(3)]);
        }

        if ($owner) {
            $owner->notify(new SystemNotification(
                'Revenue target for April exceeded by 12%.',
                'Total revenue: $148,320 against a target of $132,000.',
            ));

            $owner->notify(new SystemNotification(
                'Action plan "Reduce Food Cost Ratio" requires attention.',
                'Progress is at 20% with only 5 days remaining.',
            ));
        }

        if ($finance) {
            $finance->notify(new SystemNotification(
                'Budget variance report flagged for Q1.',
                'Maintenance department exceeded budget by 18%. Review recommended.',
            ));
        }

        if ($fo) {
            $fo->notify(new SystemNotification(
                'You have been assigned a new task.',
                '"Guest Feedback Follow-up — Room 214" has been assigned to you by Virak Lim.',
            ));
        }

        if ($hk) {
            $hk->notify(new SystemNotification(
                'KPI "Room Cleanliness Score" dropped below target.',
                'Current score: 87% — target is 92%. Please review housekeeping procedures.',
            ));
        }
    }
}
