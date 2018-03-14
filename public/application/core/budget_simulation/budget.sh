#!/bin/bash
script -c ./005_budget.out -f -t result.log &
wait
echo "success"
exit