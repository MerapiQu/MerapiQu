
const awesomeSelect = function () {

    if ($(this).data('awesome-select-initialize')) {
        return;
    }
    $(this).data('awesome-select-initialize', true);
    const $select = $(this);
    const $content = $select.children(".select-content");
    const $search = $content.children(".select-item-search");
    const $label = $("<div class='select-label'></div>");
    const $hiddenInput = $("<input type='hidden'>");

    if ($select.data("name")) {
        $hiddenInput.attr("name", $select.data("name"));
        $select.append($hiddenInput);
    }
    if (!$(".backdrop-select").length) {
        $(document.body).append(`<div class='backdrop-select'></div>`);
    }

    // initializeContent();
    initializeLabel();
    updateSelected();
    setupSearch();
    setupContentItems();

    $label.on("click", toggleContent);
    $select.on("awesome-select:hide", hideContent)
        .on("awesome-select:show", showContent)
        .on("options-update", loadingHandler)
        .on("change", handleChange)
        .on("start-loading stop-loading", loadingHandler);

    renderItems();

    function loadingHandler() {

        if ($select.data('loading')) {
            $content.children('.select-item').remove();
            $content.children('.select-item-loading').remove();
            $content.append($("<li class='select-item-loading'>Loading...</li>"));
            return;
        }

        setTimeout(() => {
            renderItems();
            if ($select.hasClass("show")) {
                $search.children("input").trigger("focus");
                scrollToActive();
                searchHandler($search.find("input").val());
            }
        }, 100);
    }

    function initializeLabel() {
        const existingLabel = $select.children('.select-label');
        if (existingLabel.length) {
            const labelHtml = existingLabel.html();
            $label.html(labelHtml).addClass(existingLabel[0].classList.value)
            existingLabel.replaceWith($label);
            $select.data("initial-label", labelHtml); // Store initial label
        } else {
            $select.append($label).data("init", true);
            $select.data("initial-label", "Select an option"); // Store initial label
        }
    }

    function setupSearch() {
        if ($search.length) {
            const $searchInput = $('<input type="text" placeholder="Search...">');
            $search.html($searchInput);
            $searchInput
                .on("input", (e) => {
                    searchHandler($searchInput.val());
                    $select.trigger("search-input", $searchInput.val());
                })
                .on("keydown", handleSearchKeyDown)
                .on("keyup", handleSearchKeyUp)
        }
    }

    function handleSearchKeyDown(event) {
        if (event.keyCode === 13) { // Enter key
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
        }
    }

    function handleSearchKeyUp(event) {
        if (event.keyCode === 27) { // Escape key
            $select.trigger("awesome-select:hide");
        }
        if (event.keyCode === 13) { // Enter key
            const highlightedItem = $content.children(".select-item.highlighted").first();
            const value = highlightedItem.data('value');
            renderItems();
            $content.children(`.select-item[data-value='${value}']`).trigger("click");
        }
    }

    function setupContentItems() {
        $content.children(".select-item").each(function () {
            const options = $select.data("options") || [];
            const $item = $(this);
            options.push({ value: $item.data("value") == undefined ? '' : `${$item.data("value")}`, label: $item.html(), disabled: $item.attr('disabled') });
            $select.data("options", options);

            if ($item.hasClass("active")) {
                $select.data("value", $item.data("value") == undefined ? '' : `${$item.data("value")}`);
            }
        }).on("click", itemClickHandler);
    }

    function hideContent(e) {
        const $target = $(this);
        $target.removeClass("show");
        $target.find(".awesome-select").removeClass("show");
        $search.find("input").val("").trigger("blur");
        $target.trigger("blur");

        if (!$target.closest('.awesome-select .select-content').length) {
            $(document).off("click", clickOutHandler);
        }
    }

    function showContent(e) {

        const $target = $(e.target);
        if ($target.attr("disabled")) {
            return;
        }
        const { left, top } = $target.offset();
        if ($target.closest('.awesome-select .select-content').length) {
            $target.children('.select-content').removeAttr('style');
            if (window.innerWidth > 720) {
                $target.children('.select-content').css({
                    position: 'fixed',
                    top: top,
                    left: left + $target.outerWidth(),
                    width: "max-content",
                });
            }
            $target.closest('.awesome-select .select-content').children('.awesome-select').removeClass("show");
        } else if ($target.children('.select-content').children('.awesome-select').length) {
            $target.children('.select-content').children('.awesome-select').each(function () {
                $(this).children('.select-content').removeAttr('style');
                if (window.innerWidth > 720) {
                    $(this).children('.select-content').css({
                        position: 'fixed',
                        top: top,
                        left: left + $target.outerWidth(),
                        width: "max-content",
                    });
                }
                $(this).removeClass("show");
            })
        }

        setTimeout(() => {
            $target.trigger("focus");
            if (!$target.closest('.awesome-select .select-content').length) {
                $(".awesome-select").trigger("awesome-select:hide");
            }
            $target.addClass("show");
            $search.find("input").trigger("focus");
            $(document).on("click", clickOutHandler);
            setTimeout(scrollToActive, 100);

        }, 100);
    }

    function renderItems() {
        const options = $select.data("options") || [];
        $content.children(".select-item").remove();
        $content.children('.select-item-loading').remove();

        const items = options.map(item => {
            const val = item.value == undefined ? '' : item.value;
            const isActive = !item.disabled && item.value == $select.data("value");
            const $item = $(`<li class="select-item${isActive ? " active" : ""}" data-value="${val}"${item.disabled ? ' disabled' : ''}>${item.label}</li>`);
            if (!item.disabled)
                $item.on("click", itemClickHandler);
            return $item;
        });

        $content.append(items);
        updateSelected();
    }

    function itemClickHandler() {
        const value = $(this).data("value") != undefined ? `${$(this).data("value")}` : '';
        $select.data("value", value).trigger("change");
        $content.children(".select-item").removeClass("active");
        $(this).addClass("active");
        renderItems();
        toggleContent();
    }

    function scrollToActive() {

        if (isElementScrollable($content)) {
            const $activeItem = $content.children(".select-item.active").first();
            $activeItem[0]?.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        } else {
            $content[0].scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
    }

    function isElementScrollable($element) {
        return $element[0].scrollHeight > $element[0].clientHeight;
    }

    function updateSelected() {
        const $activeItem = $content.children(".select-item.active").first();
        if ($activeItem.length > 0) {
            let labelHtml = $activeItem.html();
            let value = $activeItem.data('value');
            if ($label.hasClass("floating-label") && $select.data("initial-label")) {
                labelHtml = `<div style="font-size:.6rem;">${$select.data("initial-label")}</div><div style="padding:0.1rem 0.25rem;border-radius: .25rem;background:#f5f8f9;">${labelHtml}</div>`
            }
            $label.html(labelHtml);
            $hiddenInput.val(value);
        } else {
            $label.html($select.data("initial-label") || 'Select an option');
            $hiddenInput.val('');
        }

    }

    function toggleContent() {
        if ($select.hasClass("show")) {
            $select.removeClass("show");
            $select.find(".awesome-select").removeClass("show");
            $search.find("input").val("").trigger("blur");
            $select.trigger("blur");
        } else {
            $select.trigger("awesome-select:show");
        }
    }

    function clickOutHandler(event) {

        if (!$(event.target).hasClass("select-item") && (!$(event.target).closest(".awesome-select").length || $(event.target).hasClass('backdrop-select'))) {
            $select.trigger("awesome-select:hide");
        }
    }

    function searchHandler(text) {
        renderItems();

        if (!text || text.length <= 1) return;

        const $items = $content.children(".select-item");
        const regex = new RegExp(text, "gi");

        for (let i = 0; i < $items.length; i++) {
            if (regex.test($items[i].textContent)) {
                const highlightedHtml = $items[i].innerHTML.replace(new RegExp(`(?<!<[^>]*)${text}`, "gi"), "<b class='_highlight'>$&</b>");
                $($items[i]).addClass("highlighted").html(highlightedHtml);

                $items[i].scrollIntoView({ behavior: "smooth", block: "center" });

                const listOffset = $content[0].getBoundingClientRect().top;
                const itemOffset = $items[i].getBoundingClientRect().top;
                const offset = 35;
                $content[0].scrollTop += itemOffset - listOffset - offset;
                break;
            }
        }
    }

    function handleChange() {

        $content.children(".select-item").removeClass("active");

        const activeValue = $select.data("value");
        const $activeItem = $content.children(`.select-item[data-value="${activeValue}"]`);

        if ($activeItem.length) {
            $activeItem.addClass("active");
        } else {
            $select.data("value", '');
        }

        updateSelected();
    }
};


$.fn.awesomeSelect = function () {
    const $select = $(this);

    if (!$select.data("awesome-select-initialize")) {
        awesomeSelect.call($select);
    }

    return {
        el: $select,
        on(name, handler) {
            $select.on(name, handler);
            return this;
        },
        off(name, handler) {
            $select.off(name, handler);
            return this;
        },
        trigger(name, extra) {
            $select.trigger(name, extra)
        },
        options(newOptions) {
            if (newOptions) {
                $select.data("options", newOptions).trigger("options-update");
                return this;
            } else {
                return $select.data("options");
            }
        },
        val(value) {
            if (value !== undefined) {
                $select.data("value", value).trigger("change");//.trigger("awesome-select:hide");
                return this;
            } else {
                return $select.data("value");
            }
        },
        loading(isLoading) {
            if (isLoading == true) {
                $select.data("loading", isLoading).trigger("start-loading");
            } else {
                $select.data("loading", isLoading).trigger("stop-loading");
            }
            return this;
        },
        hide() {
            $select.trigger("awesome-select:hide");
            return this;
        },
        show() {
            $select.trigger("awesome-select:show");
            return this;
        },
        disabled(value) {
            $select.data("disabled", value);
            $select.trigger("awesome-select:hide");
            if ($select.data('disabled')) {
                $select.attr("disabled", true);
            } else {
                $select.removeAttr("disabled");
            }
            return this;
        },
        find: (selector = null) => {
            return $select.find(selector)
        }
    };
};

$(".awesome-select").each(function () {
    $(this).awesomeSelect();
});

function liveWatching() {
    $(".awesome-select").each(function () {
        $(this).awesomeSelect();
    });
    setTimeout(() => {
        window.requestAnimationFrame(liveWatching);
    }, 300);
}
liveWatching();

