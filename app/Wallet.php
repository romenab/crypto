<?php
require_once 'vendor/autoload.php';

class Wallet extends Api
{
    private int $money;
    private array $transactions;
    private array $owned;

    public function __construct(string $api, string $url, int $money, array $transactions = [], array $owned = [])
    {
        parent::__construct($api, $url);
        $this->money = $money;
        $this->transactions = $transactions;
        $this->owned = $owned;
    }

    public function purchase(): void
    {

        while (true) {
            $userCrypto = ucfirst(readline("Crypto you want to purchase: "));
            if ($userCrypto == "") {
                continue;
            }
            $userAmount = (int)readline("Amount $: ");
            if ($userAmount < 1) {
                continue;
            }
            break;
        }
        if ($userAmount > $this->money) {
            echo "You don't have enough money to purchase." . PHP_EOL;
            return;
        }
        $cryptoList = $this->getResponse();
        foreach ($cryptoList->data as $item) {
            if ($userCrypto === $item->name) {
                $price = $item->quote->USD->price;
                $totalCrypto = $userAmount / $price;
                $purchase = strtolower(readline("Are you sure you want to purchase $item->name for $$userAmount (y/n)? "));
                if ($purchase === "n" || $purchase === "no") {
                    return;
                }
                $this->money -= $userAmount;
                echo "Your purchased $totalCrypto $item->name for $$userAmount." . PHP_EOL;
                $this->owned($item->name, $totalCrypto);
                $this->transaction("Purchased", $item->name, $userAmount, $totalCrypto);
                return;
            }
        }
        echo "Didn't find a match!" . PHP_EOL;
    }

    public function sell()
    {
        $cryptoList = $this->getResponse();
        if (empty($this->owned)) {
            return;
        }
        $userSell = ucfirst(readline("What crypto you want to sell: "));
        if ($userSell == "") {
            return;
        }
        foreach ($this->owned as $key => $item) {
            if ($userSell === $item['cryptoName']) {
                $sell = strtolower(readline("Are you sure you want to sell $userSell (y/n)? "));
                if ($sell === "n" || $sell === "no") {
                    return;
                }
                foreach ($cryptoList->data as $crypto) {
                    if ($userSell === $crypto->name) {
                        $price = $crypto->quote->USD->price;
                        $totalDollars = $price * $item['value'];
                        $this->money += $totalDollars;
                        $this->transaction("Sold", $item["cryptoName"], $item['value'], $totalDollars);
                        echo "You sold $userSell and received $$totalDollars." . PHP_EOL;
                        unset($this->owned[$key]);
                        return;
                    }
                }
            }
        }
        echo "You don't own $userSell." . PHP_EOL;
    }

    public function transaction(string $trade, string $cryptoName, float $spent, float $received): void
    {
        $this->transactions[] = [
            "trade" => $trade,
            "cryptoName" => $cryptoName,
            "spent" => $spent,
            "received" => $received
        ];
    }

    public function owned(string $name, float $value): void
    {
        $this->owned[] = [
            "cryptoName" => $name,
            "value" => $value
        ];
    }

    public function save(string $transactionsJson): void
    {
        $data = [
            'money' => $this->money,
            'transactions' => $this->transactions,
            'owned' => $this->owned
        ];
        file_put_contents($transactionsJson, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function load(string $transactionsJson): void
    {
        $data = json_decode(file_get_contents($transactionsJson), true);
        if (isset($data['money'])) {
            $this->money = $data['money'];
        }
        if (isset($data['transactions'])) {
            $this->transactions = $data['transactions'];
        }
        if (isset($data['owned'])) {
            $this->owned = $data['owned'];
        }
    }

    public function getTransaction(): array
    {
        return $this->transactions;
    }

    public function getOwned(): array
    {
        return $this->owned;
    }

    public function getMoney(): int
    {
        return $this->money;
    }
}
