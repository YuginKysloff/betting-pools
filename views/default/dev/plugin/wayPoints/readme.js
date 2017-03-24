//$(this) - блок к которому применяем
// if (direction != 'down') вешаем события если елемент пропадает из видимости екрана

$(this).waypoint({
    handler: function(direction) {
        pv.css({
            'width': progressProcent + '%',
            'background-position': '0 ' + progressProcent + '%'
        });

        writeText();

        if (direction != 'down') {
            pv.css({
                'width': '0%',
                'background-position': '0 0'
            });
            removeText();
        };
    },
    offset: '90%'
})
