<?php namespace Aua\Fakeremail\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class Fakeremailfromexcel extends BaseCommand

{

    protected $group       = 'AUA';
    protected $name        = 'faker:excel';
    protected $description = 'Add Faker Email to Excel file for user that don\'t have email stored';



    public function run(array $params)

    {


    }

}



?>