<?php
require_once 'vendor/autoload.php';
class Tasks extends Api
{
    private function latest(): array
    {
        $cryptoList = $this->getResponse();
        $latest = [];
        $number = 1;
        foreach ($cryptoList->data as $item) {
            $latest[] = [
                'number' => $number,
                'name' => $item->name
            ];
            $number++;
            if($number > 10){
                break;
            }
        }

        return $latest;
    }

    private function search(): array
    {
        $searchInfo = [];
        $userSymbol = strtoupper(readline("Enter crypto symbol to search: "));
        $cryptoList = $this->getResponse();
        foreach ($cryptoList->data as $item) {
            if ($userSymbol === $item->symbol) {
                $searchInfo[] = [
                    "name" => $item->name,
                    "symbol" => $item->symbol,
                    "price" => number_format($item->quote->USD->price,2, '.', ','),
                    "oneHour" => round($item->quote->USD->percent_change_1h, 2),
                    "twentyFourHour" => round($item->quote->USD->percent_change_24h, 2),
                    "sevenDays" => round($item->quote->USD->percent_change_7d, 2),
                    "marketCap" => number_format($item->quote->USD->market_cap,0, '.', ',')
                ];
            }
        }
        return $searchInfo;
    }

    public function getLatest(): array
    {
        return $this->latest();
    }

    public function getSearch(): array
    {
        return $this->search();
    }
}
