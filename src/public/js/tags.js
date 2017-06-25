var tags = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '/tags?q=%QUERY',
        wildcard: '%QUERY'
    }
});
tags.initialize();

// https://github.com/twitter/typeahead.js
var elt = $('#tags');
elt.tagsinput({
    itemValue: 'value',
    itemText: 'text',
    //freeInput: true,
    confirmKeys: [13, 32, 44, 188],
    limit: 8,
    typeaheadjs: {
        name: 'tags',
        displayKey: 'text',
        source: tags.ttAdapter()
    }
});
//    elt.tagsinput('add', { "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    });