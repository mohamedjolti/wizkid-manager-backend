<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class updatepassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->ask('Enter the wizdkid email ', true);
        $password = $this->ask("Enter password");
        $confirmPassword = $this->ask("Confirm password");
        if($password ==  $confirmPassword){
            $password = Hash::make($password);
            DB::table('users')
            ->where('email', $email)
            ->update(['password' => $password]);
            $this->info("passwords updated");
        }else{
            $this->info("passwords not match");
        }


    }
}
