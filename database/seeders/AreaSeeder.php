<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = array(
            array('city_id' => '26', 'name' => 'Jawalakhel', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'Gwarko', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'Lubhu', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'Harisiddhi', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'Chapagau', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Naya Thimi Area', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Purano Thimi Area', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Sano Thimi Area', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Balaju', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Inside valley', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'kalanki', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Koteshwor', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'New Baneshwor', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Old Baneshwor', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'saddobato', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Chabahil', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Maharajgunj', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Kausaltar Area', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Bhaktapur Area', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Inside Bhaktapur', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'Inside Lalitpur', 'shipping_price' => '100'),
            array('city_id' => '14', 'name' => 'lahan', 'shipping_price' => '120'),
            array('city_id' => '69', 'name' => 'Dhangadhi', 'shipping_price' => '150'),
            array('city_id' => '26', 'name' => 'Gwarko', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Sankhamul', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'Imadol', 'shipping_price' => '100'),
            array('city_id' => '12', 'name' => 'Diktel Bazar', 'shipping_price' => '120'),
            array('city_id' => '24', 'name' => 'Sanga', 'shipping_price' => '150'),
            array('city_id' => '27', 'name' => 'Sankhamul', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'New bus park', 'shipping_price' => '100'),
            array('city_id' => '20', 'name' => 'Janakpur Dham', 'shipping_price' => '120'),
            array('city_id' => '27', 'name' => 'Boudha', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Baneshower', 'shipping_price' => '50'),
            array('city_id' => '25', 'name' => 'Suryabinayak', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Bode', 'shipping_price' => '100'),
            array('city_id' => '25', 'name' => 'Madhayapur Thimi', 'shipping_price' => '100'),
            array('city_id' => '26', 'name' => 'kumariparti', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Gausala', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Sukhedhara', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'koteshor', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Batisputali', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Bhudanilkantha', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Jadibuti', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Tinkune', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Tilganga', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Dhumbarahi', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Chakrapatha', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Gokarna', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Samakhusi', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Swyambhu', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Sitapaila', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Jamal', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Bagabazar', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Putalisadak', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Newroad', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Sundhara', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Bhrikutimandap', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Sinamangal', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Tilganga', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Pepsicola', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Gothatar', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Kadaghari', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Kapan', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Mandikatar', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Thankot', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Naikap', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Thinthana', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Satungal', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Chandragiri', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Dakshinkali', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Jorpati', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Mahantar', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Chuchepati', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Shivapuri', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Kalimati', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Tripureswor', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Teku', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Kuleswor', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Balkhu', 'shipping_price' => '100'),
            array('city_id' => '27', 'name' => 'Kritipur', 'shipping_price' => '100'),
            array('city_id' => '34', 'name' => 'Brijung', 'shipping_price' => '150'),
            array('city_id' => '34', 'name' => 'Brijung', 'shipping_price' => '150')
        );
        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}
