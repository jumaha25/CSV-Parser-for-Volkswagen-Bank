<?php

/*
This script is intended for customers of Volkswagen Bank (Germany)
It parses the CSV files of your bank transactions which you can
download from your online banking account (checking and credit card).

This script was created because the CSV files from Volkswagen Bank do not
provide a separate field for the payee which makes them very inconvenient for
importing into your favorite accounting software.

Intended for command line use. CSV file needs to be provided as argument.

Example:
php vw-multi.php vw-giro.csv
*/

ini_set("auto_detect_line_endings", true);
mb_regex_encoding("UTF-8");
$row = 0;

if (!isset($argv[1])) {
  echo "Bitte CSV-Datei als Argument übergeben:
Bsp.: php vw-multi.php vw-giro.csv\n";
  exit;
}

if (($in = fopen($argv[1], "r")) !== FALSE && ($out = fopen("vw-multi-out.csv", "w")) !== FALSE) {

    $data_out = array('Date', 'Payee', 'Outflow', 'Inflow');
    fputcsv($out, $data_out);

    while (($data = fgetcsv($in, 1000, "\t")) !== FALSE) {
        #echo mb_detect_encoding($data[2]) . "\n";
        $row++;

        if ($row < 8)
          continue;

        unset($data[0],
          $data[4],
          $data[5],
          $data[6],
          $data[7],
          $data[8],
          $data[9],
          $data[12]);

        foreach ($data as &$value) {
          $value = str_replace('"', '', $value);
          $value = str_replace("\x00", '', $value);
        }

        $data[2] = str_replace("�", 'Ü', $data[2]);
        #$data[2] = utf8_encode($data[2]);

        switch ($data[2]) {
          case 'Lastschrift':
          case 'Überweisung':
          case 'Dauerauftrag':
          case 'Gutschrift':
            $temp = explode("BIC:", $data[3]);
            $temp = preg_split("/\s{2,}/", $temp[0], -1, PREG_SPLIT_NO_EMPTY);
            $temp[0] = $temp[count($temp)-1];
            break;

          case 'Belastung Bank Card':
            $temp = explode("//", $data[3]);
            break;

          case 'Sollzinsen':
            $temp[0] = 'Volkswagen Bank';
            break;

          default:
            $temp = preg_split("/\s{2,}/", $data[3], -1, PREG_SPLIT_NO_EMPTY);
            if ($data[2] != "Gutschrift zum Stichtag" && $data[2] != "Belastung")
              $temp = preg_split("/[0-9\/ ]+$/", $temp[0], -1, PREG_SPLIT_NO_EMPTY);
        }

        $data[3] = trim($temp[0]);

        $data_out = array($data[1], $data[3], $data[10], $data[11]);
        if ($row > 7)
          fputcsv($out, $data_out);
    }

    fclose($in);
    fclose($out);

    echo "Mission accomplished!\n";
}
?>
