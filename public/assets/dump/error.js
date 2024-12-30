const random = (max, min) => {
    return Math.floor(Math.random() * (max - min) + min);
}
const getRandomHexColor = () => {
    const randomColor = Math.floor(Math.random() * 16777215).toString(16);
    return `#${randomColor.padStart(6, '0')}`;
}


function moving() {
    const el = document.querySelector(".animation-shadow");

    // Set margin boundaries (e.g., 10% margin from each edge)
    const margin = 10; // 10% margin

    // Randomize positions within the boundaries
    const posX = Math.random() * (100 - 2 * margin) + margin; // Range: margin% to (100 - margin)%
    const posY = Math.random() * (100 - 2 * margin) + margin; // Range: margin% to (100 - margin)%
    const blura = random(500, 80);
    const blurb = random(500, 80);
    const rotation = Math.random() * 360;

    // Apply the randomized positions to CSS variables
    el.style.setProperty('--pos-x', `${posX}%`);
    el.style.setProperty('--pos-y', `${posY}%`);
    el.style.setProperty('--rotation', `${rotation}deg`);
    el.style.setProperty('--blura', `${blura}px`);
    el.style.setProperty('--blurb', `${blurb}px`);
    el.style.setProperty('--color-a', getRandomHexColor());
    el.style.setProperty('--color-b', getRandomHexColor());

    // Schedule the next movement after 4 seconds
    setTimeout(() => {
        requestAnimationFrame(moving);
    }, 4000);
}

moving();



function refreshWindow() {
    window.location.reload();
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&apos;");
}

window.addEventListener("load", () => {
    //hljs.highlightAll();
    const $snippet = $("#error-snippet");

    $snippet.html(
        $snippet.text().trim().replace(/^(.*)$/gm, (line) => {

            line = escapeHtml(line);
            // Common patterns
            const patterns = [
                { regex: /^(\d+)/, replacement: `<span class="snippet-number">$1</span>` },
                {
                    regex: /(?<!<[^>]*)(\&apos\;.*?\&apos\;|\&quot\;.*?\&quot\;)/gm,
                    replacement: (e) => {
                        e = e.replace(/(\\.)/g, `<span class="snippet-char">$1</span>`);
                        return `<span class="snippet-string">${e}</span>`;
                    }
                },
                {
                    regex: /(?<!<[^>]*)((class|new|interface|enum|protected|readonly|implements)(\s+|\s+\\)(\w+))/gi,
                    replacement: `$2$3<span class="snippet-entity">$4</span>`
                },
                {
                    regex: /(?<!<[^>]*)((\w+)(\s+)(\$))/gi,
                    replacement: `<span class="snippet-entity">$2</span>$3$4`
                },
                {
                    regex: /(?<!<[^>]*)((\w+)(::)(\w+))/gi,
                    replacement: `<span class="snippet-entity">$2</span>$3$4`
                },
                {
                    regex: /(?<!<[^>]*)((:)(\s+)(\w+))/gi,
                    replacement: `$2$3<span class="snippet-entity">$4</span>`
                },
                {
                    regex: /(?<!<[^>]*)((\\)(\w+)(;))/gi,
                    replacement: `$2<span class="snippet-entity">$3</span>$4`
                },
                {
                    regex: /(?<!<[^>]*)((\|)(\w+)(\|))/gi,
                    replacement: `$2<span class="snippet-entity">$3</span>$4`
                },
                {
                    regex: /(?<!<[^>]*)((\w+)\((.*)\))/,
                    replacement: `<span class="snippet-void">$2</span>($3)`
                },
                { regex: /(?<!<[^>]*)(\$\w+)/g, replacement: `<span class="snippet-variable">$1</span>` },
                { regex: /(?<!<[^>]*)\-\&gt\;(\w+)/g, replacement: `-><span class="snippet-variable">$1</span>` },
                {
                    regex: /(?<!<[^>]*)\b(\d+)\b/g,
                    replacement: `<span class="snippet-digit">$1</span>`
                },
                {
                    regex: /(?<!<[^>]*)(class |extends |implements|final |public |private |protected |readonly |static |function |class |interface |namespace |use |string |array |int |enum )/gi,
                    replacement: `<span class="snippet-common">$1</span>`
                },
                {
                    regex: /(?<!<[^>]*)(foreach|static|return|while|throw|new|else|for|exit|echo|null|die|if)/gi,
                    replacement: `<span class="snippet-common">$1</span>`
                },
                {
                    regex: /(\/\/)(.*)/gi,
                    replacement: `<span class="snippet-comment">$1$2</span>`
                }
            ];

            // Apply patterns
            patterns.forEach(({ regex, replacement }) => {
                line = line.trim()
                line = line.replace(regex, replacement);
            });

            return `<span class='snippet-line'>${line.trimStart()}</span>`;
        })
    );

    const line = $snippet.attr("error-line");
    $snippet.find(".snippet-number").each(function () {
        if ($(this).text() == line) {
            $(this).addClass("higlight-line");
        }
    });
});