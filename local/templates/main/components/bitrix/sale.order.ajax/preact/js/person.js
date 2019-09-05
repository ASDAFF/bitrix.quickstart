/* VIEW */
function SaleOrderPersons({ LIST }) {
    return preact.h('div', { class: 'sale_order_persons' }, Object.keys(LIST).map(function (id) {
        return preact.h('div', { class: 'sale_order_person' }, [
            preact.h('input', { 
                type: 'radio', 
                id: 'PERSON_TYPE_' + LIST[id].ID, 
                name: 'PERSON_TYPE', 
                defaultValue: LIST[id].ID,
                checked: LIST[id].CHECKED
            }),
            preact.h('label', { for: 'PERSON_TYPE_' + LIST[id].ID }, LIST[id].NAME),
            LIST[id].CHECKED && preact.h('input', {
                type: 'hidden',
                name: 'PERSON_TYPE_OLD',
                defaultValue: LIST[id].ID,
            })
        ])
    }))
}