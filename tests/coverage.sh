#!/bin/bash

php -f ../tools/Tester/Tester/coverage-report.php -- -s "../src" -c coverage.dat -t "Heymaster" -o coverage.html
read -p "Press [Enter] key to exit..."

