var tags = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: 'citynames.json'
});
tags.initialize();

var elt = $('#tags');
elt.tagsinput({
    itemValue: 'value',
    itemText: 'text',
    freeInput: true,
    confirmKeys: [13, 32, 44, 188],
    typeaheadjs: {
        name: 'tags',
        displayKey: 'text',
        source: tags.ttAdapter()
    }
});
//    elt.tagsinput('add', { "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    });