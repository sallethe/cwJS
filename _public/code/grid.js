const HIGHLIGHT_CLASS_NAME = 'highlighted'

const overseer = {
    grid: null,
    words: null,
}

const c = (x, y) => ({
    x: x,
    y: y,
})

class Solver {
    grid
    answers

    constructor(grid, answers) {
        if (answers.length !== grid.dim) throw new Error('Incompatible grid.')
        for (let i in answers)
            if (i.length !== grid.dim)
                throw new Error('Incompatible grid.')
        this.grid = grid
        this.answers = answers
    }

    check() {
        const wrongs = []
        for (let x = 0; x < this.answers.length; x += 1) {
            for (let y = 0; y < this.answers[x].length; y += 1) {
                const cell = this.grid.matrix[x][y]
                if (cell.getValue() === this.answers[x][y])
                    wrongs.push(c(x, y))
            }
        }
        return wrongs
    }
}

class Cell {
    coords
    element
    isDisabled

    constructor(coords, state = false) {
        if (coords.x < 0 || coords.y < 0)
            throw new Error('Invalid coordinates.')
        this.coords = coords
        this.isDisabled = state
        this._createElement()
    }

    _createElement() {
        const e = document.createElement('input')
        e.maxLength = 1
        e.classList.add('Cell')

        e.onchange = () => e.value =
            /^[A-Za-z]$/.test(e.value) ?
                e.value.toUpperCase()
                : ''
        e.onfocus = () => {
            overseer.grid.unselect()
            overseer.grid.selectCell(this.coords)
        }
        e.onblur = () => overseer.grid.unselect()

        if (this.isDisabled) {
            e.classList.add('disabled')
            e.readOnly = true
            e.disabled = true
        }
        this.element = e

    }

    highlight() {
        if (!this.isDisabled)
            this.element.classList.add(HIGHLIGHT_CLASS_NAME)
    }

    blur() {
        if (!this.isDisabled)
            this.element.classList.remove(HIGHLIGHT_CLASS_NAME)
    }

    getValue() {
        return this.element.value
    }
}

const xor = (e1, e2) => e1 !== e2

class Grid {
    matrix
    dim

    constructor(dim, disabled = []) {
        if (dim < 0)
            throw new Error('Invalid grid dimension')
        const mtx = []
        for (let x = 0; x < dim; x += 1) {
            const l = []
            for (let y = 0; y < dim; y += 1) {
                const coords = {
                    x: x,
                    y: y,
                }
                l.push(new Cell(
                    coords,
                    disabled.find((e) => coords.x === e.x && coords.y === e.y) !== undefined))
            }
            mtx.push(l)
        }
        this.matrix = mtx
        this.dim = dim
        overseer.grid = this
    }

    generate() {
        const grid = document.getElementById('grid')
        if (!grid)
            return
        grid.innerText = ''
        for (const l of this.matrix) {
            const row = document.createElement('div')
            for (const e of l)
                row.append(e.element)
            grid.appendChild(row)
        }
    }

    selectCell = (coords) => {
        if (coords.x < 0 || coords.y < 0)
            return
        for (let x = 0; x < this.dim; x += 1)
            for (let y = 0; y < this.dim; y += 1)
                if (xor(x === coords.x, y === coords.y)) {
                    this.matrix[x][y].highlight()
                }
    }

    selectRow = (start, vertical, length) => {
        if (start.x < 0 || start.y < 0 || length < 0)
            return
        for (let x = 0; x < this.dim; x += 1) {
            for (let y = 0; y < this.dim; y += 1) {
                if ((vertical && y === start.y && x >= start.x && x <= start.x + length)
                    || (!vertical && x === start.x && y >= start.y && x <= start.y + length)) {
                    this.matrix[x][y].highlight()
                }
            }
        }
    }

    unselect = () => {
        for (const row of this.matrix)
            for (const elem of row)
                elem.blur()
    }
}

class Word {
    vertical
    coords
    length
    definition
    element

    constructor(vertical, startcoords, length, definition) {
        this.vertical = vertical
        if (startcoords.x < 0 || startcoords.y < 0) throw new Error('Invalid coordinates')
        this.coords = {
            x: startcoords.x,
            y: startcoords.y,
        }
        this.length = length
        this.definition = definition
        this._createElement()
    }

    _createElement() {
        const entry = document.createElement('div')
        const title = document.createElement('h3')
        const def = document.createElement('p')
        title.innerHTML = `${this.vertical ?
            `Colonne ${this.coords.y + 1}`
            : `Ligne ${this.coords.x + 1}`} &bull; longueur ${length}`
        def.innerHTML = this.definition
        entry.appendChild(title)
        entry.appendChild(def)
        entry.onclick = () => {
            overseer.grid.unselect()
            overseer.grid.selectRow(
                this.coords, this.vertical, this.length,
            )
        }
        entry.onblur = () => overseer.grid.unselect()
        this.element = entry
    }
}

class WordSet {
    horizontal = []
    vertical = []

    constructor(words) {
        for (let word of words) {
            if (word.vertical)
                this.vertical.push(word)
            else
                this.horizontal.push(word)
        }
    }

    generate() {
        const horDiv = document.getElementById('horizontal')
        for (let w1 of this.horizontal) {
            horDiv.appendChild(w1.element)
        }
        const verDiv = document.getElementById('vertical')
        for (let w2 of this.vertical) {
            verDiv.appendChild(w2.element)
        }
    }
}