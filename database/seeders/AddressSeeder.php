<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address\Country;
use App\Models\Address\City;
use App\Models\Address\Zone;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            'United States' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'San Francisco'],
            'France' => ['Paris', 'Lyon', 'Marseille', 'Nice', 'Bordeaux'],
            'United Kingdom' => ['London', 'Edinburgh', 'Manchester', 'Birmingham', 'Liverpool'],
            'Germany' => ['Berlin', 'Munich', 'Hamburg', 'Frankfurt', 'Cologne'],
            'Japan' => ['Tokyo', 'Osaka', 'Kyoto', 'Nagoya', 'Sapporo'],
        ];

        foreach ($countries as $countryName => $cities) {
            $country = Country::firstOrCreate(['name' => $countryName]);

            foreach ($cities as $cityName) {
                $city = City::firstOrCreate(['name' => $cityName, 'country_id' => $country->id]);

                $streets = [
                    'New York' => ['Broadway', 'Wall Street', 'Fifth Avenue', 'Madison Avenue', 'Park Avenue', 'Times Square', 'Lexington Avenue', 'Canal Street', 'Houston Street', 'Bleeker Street'],
                    'Los Angeles' => ['Hollywood Boulevard', 'Sunset Boulevard', 'Melrose Avenue', 'Rodeo Drive', 'Mulholland Drive', 'Wilshire Boulevard', 'Venice Boulevard', 'Fairfax Avenue', 'Santa Monica Boulevard', 'La Brea Avenue'],
                    'Chicago' => ['Michigan Avenue', 'State Street', 'Wacker Drive', 'LaSalle Street', 'Clark Street', 'Dearborn Street', 'Division Street', 'Lake Shore Drive', 'Randolph Street', 'Jackson Boulevard'],
                    'Houston' => ['Westheimer Road', 'Main Street', 'Richmond Avenue', 'Bellaire Boulevard', 'Kirby Drive', 'Shepherd Drive', 'Montrose Boulevard', 'San Felipe Street', 'Memorial Drive', 'Allen Parkway'],
                    'San Francisco' => ['Market Street', 'Lombard Street', 'Van Ness Avenue', 'California Street', 'Fillmore Street', 'Castro Street', 'Embarcadero', 'Haight Street', 'Divisadero Street', 'Mission Street'],
                    'Paris' => ['Champs-Élysées', 'Rue de Rivoli', 'Avenue Montaigne', 'Boulevard Saint-Germain', 'Rue de la Paix', 'Rue du Faubourg Saint-Honoré', 'Rue de la Fayette', 'Rue des Rosiers', 'Boulevard Haussmann', 'Rue de Rennes'],
                    'Lyon' => ['Rue de la République', 'Rue du Président Édouard Herriot', 'Quai Saint-Antoine', 'Rue Mercière', 'Place Bellecour', 'Rue Victor Hugo', 'Avenue Jean Jaurès', 'Cours Lafayette', 'Cours Charlemagne', 'Rue de Créqui'],
                    'Marseille' => ['Canebière', 'Rue Paradis', 'Boulevard Michelet', 'Rue Sainte', 'Rue Saint-Ferréol', 'Rue de la République', 'Quai du Port', 'Avenue du Prado', 'Boulevard Longchamp', 'Rue d\'Endoume'],
                    'Nice' => ['Promenade des Anglais', 'Avenue Jean Médecin', 'Rue Masséna', 'Cours Saleya', 'Boulevard Victor Hugo', 'Rue de France', 'Rue de la Buffa', 'Avenue des Fleurs', 'Rue Alphonse Karr', 'Avenue de Suède'],
                    'Bordeaux' => ['Rue Sainte-Catherine', 'Cours de l\'Intendance', 'Cours Victor Hugo', 'Quai des Chartrons', 'Rue du Pas-Saint-Georges', 'Rue Notre-Dame', 'Cours de Verdun', 'Rue Fondaudège', 'Cours de la Marne', 'Allées de Tourny'],
                    'London' => ['Oxford Street', 'Regent Street', 'Piccadilly', 'Bond Street', 'Baker Street', 'Shaftesbury Avenue', 'Fleet Street', 'Kings Road', 'The Strand', 'Carnaby Street'],
                    'Edinburgh' => ['Princes Street', 'Royal Mile', 'George Street', 'Queensferry Road', 'Lothian Road', 'Rose Street', 'Hanover Street', 'Leith Walk', 'Grassmarket', 'Cockburn Street'],
                    'Manchester' => ['Deansgate', 'Oxford Road', 'Market Street', 'Wilmslow Road', 'Princess Street', 'King Street', 'Portland Street', 'Canal Street', 'Chester Road', 'Piccadilly'],
                    'Birmingham' => ['Broad Street', 'New Street', 'Corporation Street', 'Colmore Row', 'Bennetts Hill', 'Temple Row', 'Navigation Street', 'High Street', 'Hagley Road', 'Bristol Street'],
                    'Liverpool' => ['Bold Street', 'Penny Lane', 'Hope Street', 'Mathew Street', 'Rodney Street', 'Water Street', 'Dale Street', 'Castle Street', 'Hanover Street', 'London Road'],
                    'Berlin' => ['Kurfürstendamm', 'Unter den Linden', 'Friedrichstraße', 'Karl-Marx-Allee', 'Alexanderplatz', 'Leipziger Straße', 'Oranienburger Straße', 'Potsdamer Platz', 'Kastanienallee', 'Torstraße'],
                    'Munich' => ['Marienplatz', 'Ludwigstraße', 'Maximilianstraße', 'Leopoldstraße', 'Sendlinger Straße', 'Kaufingerstraße', 'Residenzstraße', 'Theresienstraße', 'Prinzregentenstraße', 'Hohenzollernstraße'],
                    'Hamburg' => ['Reeperbahn', 'Jungfernstieg', 'Mönckebergstraße', 'Alsterufer', 'HafenCity', 'Gänsemarkt', 'Große Freiheit', 'Elbchaussee', 'Fuhlsbüttler Straße', 'Eimsbütteler Straße'],
                    'Frankfurt' => ['Zeil', 'Goethestraße', 'Kaiserstraße', 'Schweizer Straße', 'Berger Straße', 'Mainzer Landstraße', 'Eschersheimer Landstraße', 'Hanauer Landstraße', 'Große Eschenheimer Straße', 'Neue Mainzer Straße'],
                    'Cologne' => ['Hohe Straße', 'Schildergasse', 'Neumarkt', 'Hohenzollernring', 'Breite Straße', 'Aachener Straße', 'Ehrenstraße', 'Luxemburger Straße', 'Severinstraße', 'Venloer Straße'],
                    'Tokyo' => ['Shibuya Crossing', 'Ginza', 'Omotesando', 'Harajuku', 'Akihabara', 'Roppongi', 'Shinjuku', 'Odaiba', 'Marunouchi', 'Ueno'],
                    'Osaka' => ['Dotonbori', 'Shinsaibashi', 'Umeda', 'Namba', 'Tennoji', 'Amerikamura', 'Honmachi', 'Abeno', 'Kyobashi', 'Kitashinchi'],
                    'Kyoto' => ['Gion', 'Arashiyama', 'Kawaramachi', 'Nishiki Market', 'Pontocho', 'Kiyomizu', 'Fushimi', 'Kitano', 'Sakyo', 'Higashiyama'],
                    'Nagoya' => ['Sakae', 'Osu', 'Kanayama', 'Meieki', 'Fushimi', 'Hoshigaoka', 'Kamimaezu', 'Nakamura', 'Nishiki', 'Yabacho'],
                    'Sapporo' => ['Odori', 'Susukino', 'Nakajima', 'Kita', 'Higashi', 'Minami', 'Shiroishi', 'Atsubetsu', 'Nishi', 'Teine'],
                ];

                foreach ($streets[$cityName] as $streetName) {
                  Zone::firstOrCreate(['name' => $streetName, 'city_id' => $city->id]);
                }
            }
        }
    }
}

