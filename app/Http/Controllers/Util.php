<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

class Util extends Controller {
  public static function formatCpf($cpf) {
    if(empty($cpf)) {
      return null;
    }
    $cpf = preg_replace('/\D/', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
  }

  public static function parseDecimal($value) {
    $numeric = preg_replace('/[^\d,]/', '', $value);
    return $numeric === '' ? 0 : floatval(str_replace(',', '.', $numeric));
  }

  public static function parseDataBr($value) {
    if (!$value || trim($value) === '') {
      return null;
    }
    if (str_contains($value, '31/12/1969')) {
        return null;
    }
    foreach (['d/m/Y H:i:s', 'd/m/Y'] as $format) {
      try {
        return Carbon::createFromFormat($format, $value);
      } catch (\Exception $e) {
        continue;
      }
    }
    return null;
  }

  public static function onlyNumber(string $valor): string {
    return preg_replace('/\D/', '', $valor);
  }
}