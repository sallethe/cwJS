const HIGHLIGHT_CLASS_NAME = 'highlighted'

class Cell {
    coords
    element
    isDisabled
    grid

    constructor(coords, grid, state = false) {
        if (coords.x < 0 || coords.y < 0)
            throw new Error('Invalid coordinates.')
        this.coords = coords
        this.grid = grid
        this.isDisabled = state
        const e = document.createElement('input')
        e.maxLength = 1
        e.classList.add('Cell')
        e.onchange = () => e.value =
            /^[A-Za-z]$/.test(e.value) ?
                e.value.toUpperCase()
                : ''
        e.onfocus = () => this.grid.selectCell(this.coords)
        e.onblur = () => this.grid.unselect()
        if (state) {
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

const xor = (e1, e2) => e1 && !e2 || !e1 && e2

class Grid {
    matrix
    dim
    words

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
                l.push(new Cell(coords, this, disabled.find((e) => coords.x === e.x && coords.y === e.y) !== undefined))
            }
            mtx.push(l)
        }
        this.matrix = mtx
        this.dim = dim
    }

    generate() {
        const grid = document.getElementById('grid')
        if (!grid)
            return
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
        for (let x = 0; x < this.dim; x += 1)
            for (let y = 0; y < this.dim; y += 1)
                if ((vertical && y === start.y && x >= start.x && x <= start.x + length) || (!vertical && x === start.x && y >= start.y && x <= start.y + length))
                    this.matrix[x][y].highlight()
    }

    unselect = () => {
        for (const row of this.matrix)
            for (const elem of row)
                elem.blur()
    }
}

const c = (x, y) => ({
    x: x,
    y: y,
})

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
        this.definition = definition
        const entry = document.createElement('div')
        const title = document.createElement('h3')
        const def = document.createElement('p')
        title.innerHTML = `${vertical ?
            `Colonne ${startcoords.y + 1}` 
            : `Ligne ${startcoords.x + 1}`} &bull; longueur ${length}`
        def.innerHTML = definition
        entry.appendChild(title)
        entry.appendChild(def)
        this.element = entry
    }

    highlight() {
        this.element.classList.add(HIGHLIGHT_CLASS_NAME)
    }

    blur() {
        this.element.classList.remove(HIGHLIGHT_CLASS_NAME)
    }
}

class WordSet {
    grid
    horizontal = []
    vertical = []

    constructor(words, grid) {
        this.grid = grid
        grid.words = this
        for (let word of words) {
            if (word.vertical)
                this.vertical.push(word)
            else
                this.horizontal.push(word)
        }
    }

    generate() {
        const words = document.getElementById('words')
        if (!words) return
        const sets = words.getElementsByTagName('div')
        const horizontal = sets[0]
        for (let word of this.horizontal) {
            horizontal.appendChild(word.element)
        }
        const vertical = sets[1]
        for (let word of this.vertical) {
            vertical.appendChild(word.element)
        }
    }
}