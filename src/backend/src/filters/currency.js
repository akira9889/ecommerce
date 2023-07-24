export default function currencyJPY(value) {
  return new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'JPY', maximumSignificantDigits: 3 }).format(value);
}
