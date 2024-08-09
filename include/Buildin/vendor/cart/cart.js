
const Cart = {
    formater: new Intl.NumberFormat('ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }),
    el: null,
    data: [],
    events: {},
    on: (event, callback) => {
        if (!Cart.events[event]) Cart.events[event] = [];
        Cart.events[event].push(callback);
    },
    off: function (key, callback) {
        if (!Cart.events[key]) return;
        Cart.events[key].splice(Cart.events[key].indexOf(callback), 1);
    },
    trigger(event, params) {
        return new Promise((res, rej) => {
            const _event = new Event(event);
            const promises = (Cart.events[event] ?? []).map(async callback => {
                try {
                    if (typeof callback === 'function' && _event.cancelBubble) return;
                    return callback(_event, params); // Return the promise
                } catch (error) {
                    rej(error);
                }
            });

            // Await all promises
            Promise.all(promises).then(() => res(_event)).catch(rej);
        });
    },


    setFormater(formater) {
        Cart.formater = formater;
    },



    renderTo(el) {
        Cart.el = el;
        Cart.render();
    },

    addItem(id, { name, price = 0, data = {} } = data) {

        Cart.trigger('add', { id });
        Cart.data = Cart.retrive();
        Cart.data.push({ id, name, price, data });
        const seen = new Set();
        Cart.data = Cart.data.filter(item => {
            return seen.has(item.name) ? false : seen.add(item.name);
        });
        Cart.store();
        Cart.render();
    },

    getItem(id) {

        let find = Cart.retrive().filter(e => e.id == id);
        if (find.length > 0) return find[0];
        return false;
    },


    removeItem(id) {
        Cart.trigger('remove', { id });
        Cart.data = Cart.retrive().filter(e => e.id !== id);
        Cart.store();
        Cart.render();
    },


    store() {
        window.localStorage.setItem('cart.js', JSON.stringify(Cart.data));
    },


    retrive() {
        return JSON.parse(window.localStorage.getItem('cart.js')) ?? [];
    },


    render() {
        if (!Cart.el) return;
        $(Cart.el).html('').append(
            $(`<table class='table'>`)
                .append(
                    $(`<colgroup>`)
                        .append(
                            `<col style='width: 20px;'/>`,
                            `<col style='width: 40%;'/>`,
                            `<col style='width: 40%;'/>`,
                            `<col style='width: 120px;'/>`
                        )
                )
                .append(
                    $(`<thead>`)
                        .append(
                            $(`<tr>`).append(
                                `<th>No</th>`,
                                `<th>Name</th>`,
                                `<th>Price</th>`,
                                `<th></th>`
                            )
                        ),
                    $(`<tbody>`)
                        .append(
                            (() => {
                                if (Cart.retrive().length < 1) {
                                    return $(`<tr><td colspan='4'>${Cart.emptyHTML ?? '<div style="padding: 2rem; text-align: center; font-size: 22px; opacity: 0.7;">Cart is empty</div>'}</td></tr>`);
                                }
                                return Cart.retrive().map((item, i) => {
                                    return $('<tr>')
                                        .append(
                                            `<td style='vertical-align: middle;'>${i + 1}</td>`,
                                            `<td style='vertical-align: middle;'>${item.name}</td>`,
                                            `<td style='vertical-align: middle;'>${item.price > 0 ? Cart.formater.format(item.price) + ' / <small>years</small>' : 'free'}</td>`,
                                            $(`<td>`).append(
                                                $(`<button class='btn btn-sm btn-outline-danger py-0 px-3'><i class="fs-6 bi bi-x"></i></button>`).on('click', () => {
                                                    Cart.removeItem(item.id);
                                                    Cart.render();
                                                })
                                            )
                                        )
                                })
                            })()
                        ),

                    $(`<tfoot>`).append(
                        $(`<tr>`).append(
                            `<td colspan='3' class='text-end pe-3'>TOTAL</td>`,
                            `<td>${Cart.formater.format(Cart.retrive().reduce((accumulator, current) => { return accumulator + (current.price || 0); }, 0))}</td>`
                        )
                    )
                )
        )
    }

}

window.Cart = Cart;