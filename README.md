# CSV-Parser-for-Volkswagen-Bank

This script is intended for customers of Volkswagen Bank (Germany) It parses the CSV files of your bank transactions which you can download from your online banking account (checking and credit card).

This script was created because the CSV files from Volkswagen Bank do not provide a separate field for the payee which make it very inconvenient for importing into your favorite accounting software.

Intended for command line use. CSV file needs to be provided as argument.

Example:
php vw-multi.php vw-giro.csv
