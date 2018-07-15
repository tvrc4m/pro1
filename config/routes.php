<?php

return 
[   
    '@^/api/:module(\w+)/:group(\w+)/?$@',
    '@^/api/:module(\w+)/:group(\w+)/:controller(\w+)/?$@',
    '@^/api/:module(\w+)/:group(\w+)/:controller(\w+)/:method(\w+)/?$@',
    '@^/api/:module(\w+)/:group(\w+)/:controller(\w+)/:method(\w+)/:id(\d+)/?$@',
];