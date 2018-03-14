#!/bin/bash
if [ $1 -eq 1 ]
then
	./001_Exponential.out & 
	wait
	echo "success"
elif [ $1 -eq 2 ]
then
	./002_MIXTURE.out & 
	wait
	echo "success"

elif [ $1 -eq 3 ]
then
	./003_EPSILON.out &
	wait
	echo "success"
else
	echo "Invalid parameter"
fi
exit