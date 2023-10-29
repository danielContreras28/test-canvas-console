<?php

namespace Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CanvasPrint extends Command
{
    // create configuration method
    protected function configure()
    {
        $this->setName('canvas')
            ->setDescription('imprint the canvas')
            ->addOption('argument', null, InputOption::VALUE_OPTIONAL, 'argument of canvas');
    }

    // create execute method
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // declare variable $argument
        if ($input->getOption('argument')) {
            $arguments = explode(',', $input->getOption('argument'));
            foreach ($arguments as $argument) {
                // calls the method validateTypePanel
                $instruction = explode(' ', $argument);
                $type = $instruction[0];
                array_shift($instruction);
                $output->writeln($this->validateTypePanel($type, $instruction));
            }
        }
        return 0;
    }
    // create method validate type panel
    protected function validateTypePanel($type, $argument)
    {
        // declare variable
        switch ($type) {
            case 'C':
                // validates $argument is undefined
                if (!$argument) {
                    return 'argument is not defined, argument is required, for example: argument="C 10 10"';
                }
                //  separates the string $argument in an array by space
                $this->height = $argument[1];
                $this->width = $argument[0];
                // calls the method createCanvas
                $this->panel = $this->createCanvas($this->width + 2, $this->height + 2);
                return $this->printCanvas($this->panel);
            case 'L':
                // validates $argument is undefined
                if (!$argument) {
                    return 'argument is not defined, argument is required, for example: argument="C 10 10", "L 1 1 4 1"';
                }
                //  separates the string $argument in an array by space
                // calls the method createLine
                $this->panel = $this->createLine($argument, $this->panel);
                return $this->printCanvas($this->panel);
            case 'R':
                // validates $argument is undefined
                if (!$argument) {
                    return 'argument is not defined, argument is required, for example: argument="C 10 10", "R 1 1 5 4"';
                }
                $this->rectangle = array(
                    array($argument[0], $argument[1], $argument[2], $argument[1]),
                    array($argument[0], $argument[1], $argument[0], $argument[3]),
                    array($argument[2], $argument[1], $argument[2], $argument[3]),
                    array($argument[0], $argument[3], $argument[2], $argument[3]),
                );
                //  separates the string $argument in an array by space
                // calls the method createLine
                foreach ($this->rectangle as $line) {
                    $this->panel = $this->createLine($line, $this->panel);
                }
                return $this->printCanvas($this->panel);
            case 'B':
                // validates $argument is undefined
                if (!$argument) {
                    return 'argument is not defined, argument is required, for example: argument="C 10 10", "L 1 1 4 1"';
                }
                // calls the method BucketFill
                $this->BucketFill($argument[0], $argument[1], $argument[2]);
                return $this->printCanvas($this->panel);
            default:
                return 'type is not valid';
        }
    }
    //  function createCanvas that creates a string that forms a panel of a given height and width
    function createCanvas($height, $width) {
        $panel = array();
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                if($i === 0 || $i === $width - 1 ) {
                    $panel[$i][$j] = '-';
                } else if($j === 0 || $j === $height - 1) {
                    $panel[$i][$j] = '|';
                } else {
                    $panel[$i][$j] = ' ';
                }
            }
        }
        return $panel;
    }
    // function printCanvas that transforms an array into a string
    function printCanvas($array) {
        $printPanel = '';
        foreach ($array as $row) {
            foreach ($row as $value) {
                $printPanel .= $value;
            }
            $printPanel .= "\n";
        }
        return $printPanel;
    }

    // function createLine that creates a line in array
    function createLine($line, $panel) {
        $newPanel = $panel;
        for ($i = 0; $i < sizeof($newPanel); $i++) {
            for ($j=0; $j < sizeof($newPanel[$i]) ; $j++) {
                if($i !== 0 && $i !== sizeof($newPanel) - 1 && $j !== 0 && $j !== sizeof($newPanel[$i]) - 1) {
                    if ($i>= $line[1] && $i <= $line[3] && $j >= $line[0] && $j <= $line[2]) {
                        $newPanel[$i][$j] = '*';
                    }
                }
            }
        }
        return $newPanel;
    }
    // function BucketFill: Should fill the entire area connected to (x,y) with "colour" c. The behaviour of this is the same as that of the "bucket fill" tool in paint programs.
    function BucketFill($x, $y, $color) {
        // Check if the given coordinates are within the panel boundaries
        if ($y < 0 || $y >= count($this->panel) || $x < 0 || $x >= count($this->panel[0])) {
            return;
        }

        // Check if the current position is already filled with the desired color
        if ($this->panel[$y][$x] === '*' || $this->panel[$y][$x] === '-' || $this->panel[$y][$x] === '|' || $this->panel[$y][$x] === $color) {
            return;
        }

        $this->panel[$y][$x] = $color;

        // Recursively fill the rest of the area
        $this->BucketFill($x - 1, $y, $color); // Fill left
        $this->BucketFill($x + 1, $y, $color); // Fill right
        $this->BucketFill($x, $y - 1, $color); // Fill up
        $this->BucketFill($x, $y + 1, $color); // Fill down
    }

}