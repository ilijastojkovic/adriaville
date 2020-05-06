<?php

namespace App\Http\Controllers;

use App\Country;
use App\Exceptions\InvalidCountryName;
use App\Property;
use App\SpotlightVillas;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

class HomePageController extends Controller
{
    public const CROATIA = 'Croatia';
    public const SLOVENIA = 'Slovenia';
    public const MONTENEGRO = 'Montenegro';
    public const ALBANIA = 'Albania';
    public const BOSNIA_AND_HERZEGOVINA = 'Bosnia and Herzegovina';

    /**
     * @return \Illuminate\Contracts\View\Factory|View
     * @throws \Exception
     */
    public function index()
    {
        $croatiaTopThree = $this->fetchByCountry(self::CROATIA);
        $bosniaAndHerzegovina = $this->fetchByCountry(self::BOSNIA_AND_HERZEGOVINA);
        $sloveniaTopThree = $this->fetchByCountry(self::SLOVENIA);
        $albaniaTopThree = $this->fetchByCountry(self::ALBANIA);
        $montenegroTopThree = $this->fetchByCountry(self::MONTENEGRO);
        $spotlightVillas = SpotlightVillas::spotlightVillas();

        return view('index', [
            'croatiaTopThree' => $croatiaTopThree,
            'sloveniaTopThree' => $sloveniaTopThree,
            'montenegroTopThree' => $montenegroTopThree,
            'albaniaTopThree' => $albaniaTopThree,
            'bosniaAndHerzegovina' => $bosniaAndHerzegovina,
            'spotlightVillas' => $spotlightVillas,
        ]);
    }

    protected function propertySlider()
    {
        // tbc
    }

    public function searchProperties()
    {
        return view('search');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $searchedProperties = Property::query()
            ->select(
                'p.title',
                'p.images',
                'c.name as country'
//                'b.start_date', 'b.checkout_date', 'b.status'
            )
            ->from('properties AS p')
            ->leftJoin('bookings AS b', 'p.id', '=', 'b.property_id')
            ->leftJoin('countries AS c', 'p.country_id', '=', 'c.id')
            ->where('c.name', '=', $request->input('data')['country'])
            ->where('p.guests', '>=', $request->input('data')['guests'])
//            ->whereDate('b.checkout_date', '>=', $request->input('checkin_date'))->whereDate('b.start_date', '<=', $request->input('checkout_date'))
        ;

        return redirect()->route('searchProperties', [
            'properties' => $searchedProperties->get()->all(),
            'guests' => $request->input('data')['guests'],
            'checkinDate' => $request->input('data')['startDate']['date'],
            'checkoutDate' => $request->input('data')['endDate']['date'],
        ]);

//        return redirect('search', 302, [
//            'properties' => $searchedProperties->get()->all(),
//            'checkinDate' => $request->input('checkin_date'),
//            'checkoutDate' => $request->input('checkout_date'),
//        ]);
    }
}