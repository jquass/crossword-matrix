const puzzleSize = 15;

document.onkeydown = function (event) {
    const element = document.activeElement;
    if (element.id.match(/c[0-9]+/)) {
        const idNumber = Number(element.id.substr(1));
        let targetId;
        switch (event.keyCode) {
            case 37:
                // Left
                event.preventDefault();
                if ((idNumber - 1) % puzzleSize === 0) {
                    return;
                }
                targetId = idNumber - 1;
                break;
            case 38:
                // Up
                event.preventDefault();
                if (idNumber <= puzzleSize) {
                    return;
                }
                targetId = idNumber - puzzleSize;
                break;
            case 39:
                // Right
                event.preventDefault();
                if ((idNumber % puzzleSize === 0)) {
                    return;
                }
                targetId = idNumber + 1;
                break;
            case 40:
                // Down
                event.preventDefault();
                if (idNumber >= puzzleSize * puzzleSize - puzzleSize) {
                    return;
                }
                targetId = idNumber + puzzleSize;
                break;
        }

        if (typeof targetId !== 'undefined') {
            const targetCell = 'c' + targetId;
            const target = document.getElementById(targetCell);
            target.focus();
        }
    }
};

const cellValueChange = function (id, value) {
    const element = document.getElementById(id);
    const idNumber = Number(element.id.substr(1));
    const symmetricalIdNumber = (puzzleSize * puzzleSize) - idNumber + 1;
    const symmetricalId = 'c' + symmetricalIdNumber;
    const symmetricalElement = document.getElementById(symmetricalId);
    const symmetricalValue = symmetricalElement.getAttribute('value');

    if (value.trim() === '*') {
        element.style.backgroundColor = 'black';
        symmetricalElement.style.backgroundColor = 'black';
        symmetricalElement.setAttribute('value', '*');
    } else {
        element.style.backgroundColor = 'white';
        if (symmetricalValue.trim() === '*') {
            symmetricalElement.style.backgroundColor = 'white';
            symmetricalElement.setAttribute('value', ' ');
        }
    }
}