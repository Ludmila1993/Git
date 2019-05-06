<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$children = 150;
$grown = 30;
abstract class people
{
    public $weight;
    //   public $responsibility;
}

class grown extends people
{
    //  public $responsibility = 1;
    public  $weight = 2;
}

class children extends people
{
    public  $weight = 1;
    //  public $responsibility = 0;
}

class Coast
{
    protected $registry = [];

    public function set($key, $value = null)
    {
        if (isset($this->registry[$key])) {
            throw new Exception('Element with key:' . $key . ' already exists!!!');

            return false;
        }

        $this->registry[$key] = $value;

        return $this;
    }

    public function get($key)
    {
        if (!isset($this->registry[$key])) {
            return false;
        }

        return $this->registry[$key];
    }

    public function randomKey()
    {
        $key = array_rand($this->registry, 1);
        return $key;
    }

    public function uns($key)
    {
        if (!isset($this->registry[$key])) {
            throw new Exception('Element with key:' . $key . ' not exists!!!');
        }
        unset($this->registry[$key]);

        return $this;
    }

    public function checkGrovn()
    {
        $i =0;
        foreach ($this->registry as $key=>$value){
            if(get_class($value) == 'grown')
                $i++;
        }
        return $i;
    }

    public function checkChildren()
    {
        $i =0;
        foreach ($this->registry as $key=>$value){
            if(get_class($value) == 'children')
                $i++;
        }
        return $i;
    }

    public function checkSomebady()
    {
        $i =0;
        foreach ($this->registry as $key=>$value){
            if(get_class($value) == 'children')
                $i++;
            if(get_class($value) == 'grown')
                $i++;
        }
        if ($i > 0){
            return true;
        }else{
            return false;
        }
    }
}

class Boat extends Coast
{
    public function checkWeight()
    {
        $sumWeight = 0;
        foreach ($this->registry as $key=>$value){
            $sumWeight += $value ->weight;
        }
        return $sumWeight;
    }

    public function shipping($startCoast, $boat, $finCoast)
    {
        while (($boat->checkWeight() < 3) && $startCoast->checkSomebady()) {
            $l = $startCoast->randomKey();
            $boat->set($l, $startCoast->get($l));
            $startCoast->uns($l);
        }
        //проверка может ли ложка плыть,
        if (((($boat->checkWeight() < 5) && ($boat->checkGrovn() > 0)) && (($startCoast->checkChildren() > 0 && $startCoast->checkGrovn()) || $startCoast->checkGrovn())) || !$startCoast->checkSomebady()) {
            //перевозка и высадка
            while ($boat->checkWeight() > 0) {
                $l = $boat->randomKey();
                $finCoast->set($l, $boat->get($l));
                $boat->uns($l);
            }
        } else {
            //высадка обратно и повторная загрузка
            while ($boat->checkWeight() > 0) {
                $l = $boat->randomKey();
                $startCoast->set($l, $boat->get($l));
                $boat->uns($l);
                return $this->shipping($startCoast, $boat, $finCoast);
            }
        }
    }

    public function beakGrown ($startCoast, $boat, $finCoast)
    {
        //погрузка
        while ($boat->checkWeight() < 2) {
            $l = $startCoast->randomKey();
            $boat->set($l, $startCoast->get($l));
            $startCoast->uns($l);
        }
        //проверка может ли ложка плыть,
        if ($boat->checkGrovn() > 0) {
            //перевозка и высадка
            while ($boat->checkWeight() > 0) {
                $l = $boat->randomKey();
                $finCoast->set($l, $boat->get($l));
                $boat->uns($l);
            }
        } else {
            //высадка обратно
            while ($boat->checkWeight() > 0) {
                $l = $boat->randomKey();
                $startCoast->set($l, $boat->get($l));
                $boat->uns($l);
            }
        }
    }

}

$men = new grown();
$kids = new children();
$leftCoast = new Coast();
$boat = new Boat();
$rightCost = new Coast();
// data loading
for ($i = $children; $i > 0; $i--){
    $leftCoast->set($i,$kids);
}
for ($i = $children+$grown; $i > $children; $i--){
    $leftCoast->set($i,$men);
}
$numberShipping = 1;
// Sipping
if (($grown < 1 && $children < 1)){
    echo 'Некого перевозить';
}elseif(($grown < 2 && $children > 2) || ($grown < 3 && $children > 4)){
    echo 'Невозмижно выполнить условие';
}else {
    while (!(($leftCoast->checkGrovn() < 2) && ($leftCoast->checkChildren() < 3 ))) {
        if ($leftCoast->checkGrovn() < 2) {
            $boat->beakGrown($rightCost, $boat, $leftCoast);
            $numberShipping++;
        } else {
            $boat->shipping($leftCoast, $boat, $rightCost);
            $numberShipping++;
        }
    }
    $boat->shipping($leftCoast, $boat, $rightCost);
    echo 'Всех перевезли' . '<br/>';
    echo 'Дети = ' . $rightCost->checkChildren() . '<br/>';
    echo 'Взрослые = ' . $rightCost->checkGrovn() . '<br/>';
    echo 'Количество перевозок = ' . $numberShipping;
}

