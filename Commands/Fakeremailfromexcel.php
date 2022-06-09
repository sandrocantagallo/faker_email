<?php namespace Fakeremail\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;



class Fakeremailfromexcel extends BaseCommand

{

    protected $group       = 'AUA';
    protected $name        = 'faker:excel';
    protected $description = 'Add Faker Email to Excel file for user that don\'t have email stored';
    protected $usage = 'faker:excel [filename] [rows to skip] [columns for user@email] [column where email is] [column where mobile phone is]';


    public function run(array $params)

    {
        try {

            CLI::write(CLI::color('AUA Faker Email to Excel', 'green')." - ".CLI::color(date("d/m/Y", time()), 'purple'));

            $this->checkparams($params);

            $ay_emails = explode(",", $params[3]);
            $ay_usermail = explode(",", $params[2]);
            $ay_phone = explode(",", $params[4]);

            CLI::write(CLI::color('Column with emails: ', 'green')." - ".CLI::color(print_r($ay_emails, true), 'purple'));
    
            $inputFileName = WRITEPATH . "/uploads/".$params[0];
            $excelReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $excelReader->load($inputFileName);
    
            CLI::write($spreadsheet->getSheetCount() . ' worksheet' . (($spreadsheet->getSheetCount() == 1) ? '' : 's') . ' loaded');
    
            $loadedSheetNames = $spreadsheet->getSheetNames();
    
            foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
                CLI::write($sheetIndex . ' -> ' . $loadedSheetName);
            }
    
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            if (is_array($sheetData)) {
                foreach ($sheetData AS $key => $data) {
                    if ($key > $params[1]) {
                        //controllo i campi email per vedere se sono vuoti
                        $skip_row = false;
                        foreach ($ay_emails AS $email) {
                            if ($data[$email] != '') {
                                $skip_row = true;
                            }
                        }
                        if (!$skip_row) {
                            //devo generare la email
                            $fake_email = "";
                            foreach ($ay_usermail AS $user_part) {
                                $fake_email .= preg_replace("/[^A-Za-z0-9]/","", $data[$user_part]);
                            }
                            CLI::write(CLI::color('New Fake Email: ', 'green')." - ".CLI::color($fake_email."@y-b.it", 'purple'));
                            //aggiorno la riga solo se hanno almeno un numero di telefono valido
                            $phone_check = false;
                            foreach ($ay_phone AS $phone) {
                                if ($data[$phone] != '') {
                                    $phone_check = true;
                                }
                            }
                            if ($phone_check) {
                                //adesso modifico la rica del file Excel indicato
                                $spreadsheet->getActiveSheet()->getCell($ay_emails[0].$key)->setValue($fake_email."@y-b.it");
                                CLI::write(CLI::color('Email stored into Excel: ', 'green')." - ".CLI::color("OK", 'green'));
                            } else {
                                CLI::write(CLI::color('Email stored into Excel: ', 'green')." - ".CLI::color("KO", 'red')); 
                            }
                            
                        }
                    }
                }

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save(WRITEPATH . "/uploads/new_".$params[0]);
            }
        } catch (\Exception $e) {
            CLI::write(CLI::color('Fatal Error: ', 'red')." - ".CLI::color(print_r($e->getMessage(), true), 'red'));

        }
        
    }

    private function checkparams($params) {

        if (count($params)<5) {
            throw new Exception("Missing params - type Help to understant how to use command.");
        }

        if (isset($params[0])) {
            CLI::write(CLI::color('File name: ', 'green')." - ".CLI::color($params[0], 'purple'));
        } else {
            throw new Exception("No file name.");
        }

        if (isset($params[1]) AND (is_numeric($params[1]))){
            CLI::write(CLI::color('Rows to skip: ', 'green')." - ".CLI::color($params[1], 'purple'));
        } else {
            throw new Exception("param 1 must be numeric.");
        }

    }

}



?>