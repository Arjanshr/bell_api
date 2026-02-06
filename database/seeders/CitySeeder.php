<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = array(
            array('name' => 'Taplejung', 'province_id' => '1', 'zip_code' => '57500'),
            array('name' => 'Panchthar', 'province_id' => '1', 'zip_code' => '57400'),
            array('name' => 'Ilam', 'province_id' => '1', 'zip_code' => '57300'),
            array('name' => 'Jhapa', 'province_id' => '1', 'zip_code' => '57200'),
            array('name' => 'Sankhuwasabha', 'province_id' => '1', 'zip_code' => '56900'),
            array('name' => 'Tehrathum', 'province_id' => '1', 'zip_code' => '57100'),
            array('name' => 'Bhojpur', 'province_id' => '1', 'zip_code' => '57000'),
            array('name' => 'Dhankuta', 'province_id' => '1', 'zip_code' => '56800'),
            array('name' => 'Morang', 'province_id' => '1', 'zip_code' => '56600'),
            array('name' => 'Sunsari', 'province_id' => '1', 'zip_code' => '56700'),
            array('name' => 'Solukhumbu', 'province_id' => '1', 'zip_code' => '56000'),
            array('name' => 'Khotang', 'province_id' => '1', 'zip_code' => '56200'),
            array('name' => 'Okhaldhunga', 'province_id' => '1', 'zip_code' => '56100'),
            array('name' => 'Udayapur', 'province_id' => '1', 'zip_code' => '56300'),
            array('name' => 'Saptari', 'province_id' => '2', 'zip_code' => '56400'),
            array('name' => 'Siraha', 'province_id' => '2', 'zip_code' => '56500'),
            array('name' => 'Dolakha', 'province_id' => '3', 'zip_code' => '45500'),
            array('name' => 'Ramechhap', 'province_id' => '3', 'zip_code' => '45400'),
            array('name' => 'Sindhuli', 'province_id' => '3', 'zip_code' => '45300'),
            array('name' => 'Dhanusha', 'province_id' => '2', 'zip_code' => '45600'),
            array('name' => 'Mahottari', 'province_id' => '2', 'zip_code' => '45700'),
            array('name' => 'Sarlahi', 'province_id' => '2', 'zip_code' => '45800'),
            array('name' => 'Sindhupalchowk', 'province_id' => '3', 'zip_code' => '45200'),
            array('name' => 'Kavrepalanchowk', 'province_id' => '3', 'zip_code' => '45100'),
            array('name' => 'Bhaktapur', 'province_id' => '3', 'zip_code' => '44800'),
            array('name' => 'Lalitpur', 'province_id' => '3', 'zip_code' => '44700'),
            array('name' => 'Kathmandu', 'province_id' => '3', 'zip_code' => '44600'),
            array('name' => 'Rasuwa', 'province_id' => '3', 'zip_code' => '45000'),
            array('name' => 'Nuwakot', 'province_id' => '3', 'zip_code' => '44900'),
            array('name' => 'Dhading', 'province_id' => '3', 'zip_code' => '45110'),
            array('name' => 'Rautahat', 'province_id' => '2', 'zip_code' => '44500'),
            array('name' => 'Makawanpur', 'province_id' => '3', 'zip_code' => '44100'),
            array('name' => 'Bara', 'province_id' => '2', 'zip_code' => '44400'),
            array('name' => 'Parsa', 'province_id' => '2', 'zip_code' => '44300'),
            array('name' => 'Chitwan', 'province_id' => '3', 'zip_code' => '44200'),
            array('name' => 'Gorkha', 'province_id' => '4', 'zip_code' => '34000'),
            array('name' => 'Lamjung', 'province_id' => '4', 'zip_code' => '33600'),
            array('name' => 'Tanahun', 'province_id' => '4', 'zip_code' => '33900'),
            array('name' => 'Manang', 'province_id' => '4', 'zip_code' => '33500'),
            array('name' => 'Kaski', 'province_id' => '4', 'zip_code' => '33700'),
            array('name' => 'Syangja', 'province_id' => '4', 'zip_code' => '33800'),
            array('name' => 'Nawalparasi (East)', 'province_id' => '4', 'zip_code' => '33000'),
            array('name' => 'Palpa', 'province_id' => '5', 'zip_code' => '32500'),
            array('name' => 'Rupandehi', 'province_id' => '5', 'zip_code' => '32900'),
            array('name' => 'Gulmi', 'province_id' => '5', 'zip_code' => '32600'),
            array('name' => 'Kapilvastu', 'province_id' => '5', 'zip_code' => '32800'),
            array('name' => 'Arghakhanchi', 'province_id' => '5', 'zip_code' => '32700'),
            array('name' => 'Mustang', 'province_id' => '4', 'zip_code' => '33100'),
            array('name' => 'Myagdi', 'province_id' => '4', 'zip_code' => '33200'),
            array('name' => 'Parbat', 'province_id' => '4', 'zip_code' => '33400'),
            array('name' => 'Baglung', 'province_id' => '4', 'zip_code' => '33300'),
            array('name' => 'Pyuthan', 'province_id' => '5', 'zip_code' => '22300'),
            array('name' => 'Rukum West ', 'province_id' => '6', 'zip_code' => '22100'),
            array('name' => 'Rolpa', 'province_id' => '5', 'zip_code' => '22110'),
            array('name' => 'Dang', 'province_id' => '5', 'zip_code' => '22400'),
            array('name' => 'Salyan', 'province_id' => '6', 'zip_code' => '22200'),
            array('name' => 'Jajarkot', 'province_id' => '6', 'zip_code' => '21500'),
            array('name' => 'Banke', 'province_id' => '5', 'zip_code' => '21900'),
            array('name' => 'Bardiya', 'province_id' => '5', 'zip_code' => '21800'),
            array('name' => 'Surkhet', 'province_id' => '6', 'zip_code' => '21700'),
            array('name' => 'Dailekh', 'province_id' => '6', 'zip_code' => '21600'),
            array('name' => 'Dolpa', 'province_id' => '6', 'zip_code' => '21400'),
            array('name' => 'Jumla', 'province_id' => '6', 'zip_code' => '21200'),
            array('name' => 'Kalikot', 'province_id' => '6', 'zip_code' => '21300'),
            array('name' => 'Mugu', 'province_id' => '6', 'zip_code' => '21100'),
            array('name' => 'Humla', 'province_id' => '6', 'zip_code' => '21000'),
            array('name' => 'Bajura', 'province_id' => '7', 'zip_code' => '10600'),
            array('name' => 'Achham', 'province_id' => '7', 'zip_code' => '10700'),
            array('name' => 'Kailali', 'province_id' => '7', 'zip_code' => '10900'),
            array('name' => 'Doti', 'province_id' => '7', 'zip_code' => '10800'),
            array('name' => 'Bajhang', 'province_id' => '7', 'zip_code' => '10500'),
            array('name' => 'Darchula', 'province_id' => '7', 'zip_code' => '10100'),
            array('name' => 'Baitadi', 'province_id' => '7', 'zip_code' => '10200'),
            array('name' => 'Dadeldhura', 'province_id' => '7', 'zip_code' => '10300'),
            array('name' => 'Kanchanpur', 'province_id' => '7', 'zip_code' => '10400'),
            array('name' => 'Nawalparasi (West)', 'province_id' => '5', 'zip_code' => '33010'),
            array('name' => 'Rukum East ', 'province_id' => '5', 'zip_code' => '22120')
        );
        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
