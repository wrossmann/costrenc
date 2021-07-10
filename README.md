# Costrenc - Co[nvert] Str[eam] Enc[oding]

Convert the encoding of data in-flight with PHP's input streams.

## Requirements

* The `mbstring` extension.

## Installation

```
composer install wrossmann/costrenc
```

## Usage

```
use wrossmann\Costrenc\Costrenc;

// 1. Define the filter parameters
class UTF16LEtoUTF8 extends Costrenc {
    protected $in_charset = 'UTF-16LE';
    protected $out_charset = 'UTF-8';
}

// 2. Open your stream
$fh = fopen('my_utf16_file.txt');

// 3. Append the filter
stream_filter_register('UTF16LEtoUTF8', 'UTF16LEtoUTF8');
stream_filter_append($fh, 'UTF16LEtoUTF8', STREAM_FILTER_READ);

// 4. Use the stream normally
while( $line = fgets($fh) ) {
  echo $line;
}
```

### Error Handling

Under a very narrow set of circumstances this filter can detect encoding errors. When this happens the default behaviour is to raise an `E_USER_WARNING` and make a best-effort at converting the encoding of the subsequent data, though no guarantees can be made.

You can override this behaviour or otherwise change the method of message dispatch by overriding the `error_dispatch()` function in a manner such as below.

```
class UTF16LEtoUTF8 extends Costrenc {
    protected $in_charset = 'UTF-16LE';
    protected $out_charset = 'UTF-8';

    protected function raise_error($message) {
        thrown new \Exception($message);
    }	
}
```

That said, it's worth noting that reliably detecting encoding errors is generally not feasible and this filter will munge malformed data about as happily as any other library.

YMMV

