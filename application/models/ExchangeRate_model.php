<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExchangeRate_model extends CI_Model {

    protected $table = 'exchange_rates';

    public function getTodayRates($baseCurrency = 'INR')
    {
        return $this->db
            ->where('from_currency', $baseCurrency)
            ->where('rate_date', date('Y-m-d'))
            ->get($this->table)->result();
    }

    public function getLatestRates($baseCurrency = 'INR')
    {
        // Get latest rate for each currency pair
        $rows = $this->db->query("
            SELECT er.* 
            FROM exchange_rates er
            INNER JOIN (
                SELECT from_currency, to_currency, MAX(rate_date) AS max_date
                FROM exchange_rates
                WHERE from_currency = ?
                GROUP BY from_currency, to_currency
            ) latest ON er.from_currency = latest.from_currency 
                     AND er.to_currency = latest.to_currency 
                     AND er.rate_date = latest.max_date
            ORDER BY er.to_currency
        ", [$baseCurrency])->result();
        return $rows;
    }

    public function getRate($fromCurrency, $toCurrency, $date = null)
    {
        if ($date === null) $date = date('Y-m-d');
        $row = $this->db
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('rate_date <=', $date)
            ->order_by('rate_date', 'DESC')
            ->limit(1)
            ->get($this->table)->row();
        return $row ? (float)$row->rate : 1.0;
    }

    public function getRateMap($baseCurrency = 'INR')
    {
        $rows = $this->getLatestRates($baseCurrency);
        $map = [];
        foreach ($rows as $r) {
            $map[$r->to_currency] = (float)$r->rate;
        }
        $map[$baseCurrency] = 1.0; // self
        return $map;
    }

    public function upsert($fromCurrency, $toCurrency, $rate, $date, $updatedBy = null, $isManual = 0)
    {
        $existing = $this->db->get_where($this->table, [
            'from_currency' => $fromCurrency,
            'to_currency'   => $toCurrency,
            'rate_date'     => $date,
        ])->row();

        $data = [
            'from_currency' => $fromCurrency,
            'to_currency'   => $toCurrency,
            'rate'          => $rate,
            'rate_date'     => $date,
            'is_manual'     => $isManual,
            'updated_by'    => $updatedBy,
        ];

        if ($existing) {
            $this->db->where('id', $existing->id)->update($this->table, $data);
        } else {
            $this->db->insert($this->table, $data);
        }
        return true;
    }

    public function getAllForManage()
    {
        return $this->db
            ->select('er.*, cu.name AS currency_name, cu.symbol AS currency_symbol, u.name AS updated_by_name')
            ->from($this->table . ' er')
            ->join('currencies cu', 'cu.code = er.to_currency', 'left')
            ->join('users u', 'u.id = er.updated_by', 'left')
            ->order_by('er.rate_date', 'DESC')
            ->order_by('er.to_currency', 'ASC')
            ->get()->result();
    }

    public function convertAmount($amount, $fromCurrency, $toCurrency, $baseCurrency = 'INR')
    {
        if ($fromCurrency === $toCurrency) return $amount;

        // Convert to base first, then to target
        $toBase   = $fromCurrency === $baseCurrency ? 1.0 : (1 / $this->getRate($baseCurrency, $fromCurrency));
        $fromBase = $this->getRate($baseCurrency, $toCurrency);

        return $amount * $toBase * $fromBase;
    }
}
