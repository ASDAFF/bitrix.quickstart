function StartShopFlyBasket(parameters)
{
    if (parameters === undefined) parameters = {};
    if (parameters['basket'] === undefined) parameters['basket'] = '.basket';
    if (parameters['switcher'] === undefined) parameters['switcher'] = '.switcher';
    if (parameters['sections'] === undefined) parameters['sections'] = '.sections';
    if (parameters['section'] === undefined) parameters['section'] = '.section';
    if (parameters['animationSpeed'] === undefined) parameters['animationSpeed'] = 500;

    var current = null;

    this.constructor.prototype.switchSectionByNumber = function (number, animate) {
        if (animate === undefined) animate = true;

        if (current != number) {
            var section = $(parameters['basket'] + ' ' + parameters['sections'] + ' ' + parameters['section']).eq(number);
            var sectionWidth = section.width();

            $(parameters['basket'] + ' ' + parameters['sections'] + ' ' + parameters['section']).css('display', 'none');
            section.css('display', 'block');

            if (animate) {
                $(parameters['basket'] + ' ' + parameters['sections']).stop().animate({'width': sectionWidth + 'px'}, parameters['animationSpeed']);
            } else {
                $(parameters['basket'] + ' ' + parameters['sections']).css('width', sectionWidth + 'px');
            }

            current = number;
        } else {
            this.closeSections(animate);
        }
    };

    this.constructor.prototype.switchSectionByID = function (id, animate) {
        if (animate === undefined) animate = true;

        this.switchSectionByNumber($(parameters['basket'] + ' ' + parameters['sections'] + ' ' + parameters['section'] + '#' + id).index(), animate);
    };

    this.constructor.prototype.closeSections = function (animate) {
        if (animate === undefined) animate = true;

        if (animate) {
            $(parameters['basket'] + ' ' + parameters['sections']).stop().animate({'width': '0px'}, parameters['animationSpeed'], function(){
                $(parameters['basket'] + ' ' + parameters['sections'] + ' ' + parameters['section']).css('display', 'none');
                current = null;
            });
        } else {
            $(parameters['basket'] + ' ' + parameters['sections']).css('width', '0px');
            $(parameters['basket'] + ' ' + parameters['sections'] + ' ' + parameters['section']).css('display', 'none');
            current = null;
        }
    }
}