<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(['email'=>'yara@gmail.com'],[
            'name'=>'yara',
            'password'=>bcrypt('11111111'),
            'role'=>'super_admin',
        ]);
        Admin::updateOrCreate(['email'=>'yara2@gmail.com'],[
            'name'=>'yara2',
            'password'=>bcrypt('11111111'),
            'role'=>'admin',
            'section'=>'restaurant'
        ]);
        Admin::updateOrCreate(['email'=>'yara3@gmail.com'],[
            'name'=>'yara3',
            'password'=>bcrypt('11111111'),
            'role'=>'sub_admin',
            'section'=>'restaurant'
        ]);
        Admin::updateOrCreate(['email'=>'yara4@gmail.com'],[
            'name'=>'yara4',
            'password'=>bcrypt('11111111'),
            'role'=>'admin',
            'section'=>'hotel'
        ]);
        Admin::updateOrCreate(['email'=>'yara5@gmail.com'],[
            'name'=>'yara5',
            'password'=>bcrypt('11111111'),
            'role'=>'sub_admin',
            'section'=>'hotel'
        ]);
        Admin::updateOrCreate(['email'=>'yara6@gmail.com'],[
            'name'=>'yara6',
            'password'=>bcrypt('11111111'),
            'role'=>'admin',
            'section'=>'tour'
        ]);
        Admin::updateOrCreate(['email'=>'yara7@gmail.com'],[
            'name'=>'yara7',
            'password'=>bcrypt('11111111'),
            'role'=>'sub_admin',
            'section'=>'tour'
        ]);
        Admin::updateOrCreate(['email'=>'yara8@gmail.com'],[
            'name'=>'yara8',
            'password'=>bcrypt('11111111'),
            'role'=>'admin',
            'section'=>'travel'
        ]);
        Admin::updateOrCreate(['email'=>'yara9@gmail.com'],[
            'name'=>'yara9',
            'password'=>bcrypt('11111111'),
            'role'=>'sub_admin',
            'section'=>'travel'
        ]);
    }
}
