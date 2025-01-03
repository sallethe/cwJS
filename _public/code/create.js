const DEFAULT_GRID_DIM = 5

class EditableGrid extends Grid {

    constructor(
        initializer = [],
        dim = DEFAULT_GRID_DIM) {
        if (initializer) {
            super(initializer.length)
            for (let x = 0; x < this.dim; x += 1)
                for (let y = 0; y < this.dim; y += 1)
                    this.matrix[x][y].element.value = initializer[x][y]
        } else {
            super(dim)
        }
    }

    resizeGrid(dim) {
        if (dim < 1) return
        if (dim === this.dim) return

        if (dim < this.dim) {
            while (this.matrix.length > dim) {
                this.matrix.pop()
            }
            for (let x = 0; x < dim; x += 1)
                while (this.matrix[x].length > dim) {
                    this.matrix[x].pop()
                }
        }

        if (dim > this.dim) {
            for (let x = 0; x < this.dim; x += 1) {
                for (let y = this.dim; y < dim; y += 1) {
                    this.matrix[x].push(new Cell({
                        x: x,
                        y: y,
                    }))
                }
            }

            for (let x = this.dim; x < dim; x += 1) {
                const l = []
                for (let y = 0; y < dim; y += 1) {
                    l.push(new Cell({
                        x: x,
                        y: y,
                    }))
                }
                this.matrix.push(l)
            }
        }
        this.dim = dim
        this.generate()
        this.unselect()
    }

    convertIntoAnswer() {
        const res = []
        for (let x of this.matrix) {
            const l = []
            for (let y of x) {
                l.push(y.getValue())
            }
            res.push(l)
        }
        return res
    }
}

class EditableWordSet {
    horList
    verList

    constructor(initializer = []) {
        this.horList = document.getElementById('horizontal')
        this.verList = document.getElementById('vertical')

        const value =
            document.getElementById('dimensions')

        document.getElementById('plus').onclick = () => {
            overseer.grid.resizeGrid(overseer.grid.dim + 1)
            value.innerText = String(overseer.grid.dim)
        }
        document.getElementById('minus').onclick = () => {
            overseer.grid.resizeGrid(overseer.grid.dim - 1)
            value.innerText = String(overseer.grid.dim)
        }

        value.innerText = String(overseer.grid.dim)
        this.verList.getElementsByTagName('button')[0].onclick =
            () => this.createVerticalEntry()
        this.verList.getElementsByTagName('button')[1].onclick =
            () => this.removeVerticalEntry()
        this.horList.getElementsByTagName('button')[0].onclick =
            () => this.createHorizontalEntry()
        this.horList.getElementsByTagName('button')[1].onclick =
            () => this.removeHorizontalEntry()
        overseer.words = this

        if (initializer) {
            this._initialize(initializer)
        }
    }

    _initialize(initializer) {
        for (let word of initializer) {
            if (word.vertical) {
                this.createVerticalEntry(
                    word.length,
                    word.coords.x,
                    word.coords.y,
                    word.definition,
                )
            } else {
                this.createHorizontalEntry(
                    word.length,
                    word.coords.x,
                    word.coords.y,
                    word.definition,
                )
            }
        }
    }

    _createEntry(dlen = 0,
                 dx = 0,
                 dy = 0,
                 ddef = 'Lorem Ipsum') {
        const e = document.createElement('div')
        e.classList.add('WordEntry')

        const len = document.createElement('input')
        len.type = 'number'
        len.placeholder = 'Longueur'
        len.value = String(dlen)
        const x = document.createElement('input')
        x.type = 'number'
        x.placeholder = 'X'
        x.min = '0'
        x.value = String(dx)
        const y = document.createElement('input')
        y.type = 'number'
        y.placeholder = 'Y'
        y.min = '0'
        y.value = String(dy)
        const def = document.createElement('input')
        def.type = 'text'
        def.placeholder = 'Définition'
        def.value = String(ddef)

        e.appendChild(len)
        e.appendChild(x)
        e.appendChild(y)
        e.appendChild(def)

        return e
    }

    createVerticalEntry(
        dlen = 0,
        dx = 0,
        dy = 0,
        ddef = 'Lorem Ipsum',
    ) {
        const e = this._createEntry(dlen, dx, dy, ddef)
        this.verList.insertBefore(e,
            this.verList.querySelector('.ButtonsSet'))
    }

    createHorizontalEntry(
        dlen = 0,
        dx = 0,
        dy = 0,
        ddef = 'Lorem Ipsum',
    ) {
        const e = this._createEntry(dlen, dx, dy, ddef)
        this.horList.insertBefore(e,
            this.horList.querySelector('.ButtonsSet'))
    }

    removeHorizontalEntry() {
        const children = this.horList.children
        const len = children.length
        if (len > 1) children[len - 2].remove()
    }

    removeVerticalEntry() {
        const children = this.verList.children
        const len = children.length
        if (len > 1) children[len - 2].remove()
    }

    _entryToWord(entry, isVertical = true) {
        if (entry.children.length === 4) {
            const len = entry.children[0]
            const x = entry.children[1]
            const y = entry.children[2]
            const def = entry.children[3]
            return {
                vertical: isVertical,
                coords: c(Number(x.value), Number(y.value)),
                length: Number(len.value),
                definition: def.value,
            }
        }
        return undefined
    }

    convertIntoList() {
        const res = []
        for (const entry of this.verList.children) {
            const word = this._entryToWord(entry, true)
            if (word) res.push(word)
        }
        for (const entry of this.horList.children) {
            const word = this._entryToWord(entry, false)
            if (word) res.push(word)
        }
        return res
    }
}

class Verificator {
    words
    grid

    error

    wordsInput
    gridInput
    titleInput
    dimInput
    countInput

    submitter

    constructor(words, grid) {
        this.wordsInput = document.getElementById('wordse')
        this.gridInput = document.getElementById('gride')
        this.titleInput = document.getElementById('title')
        this.dimInput = document.getElementById('dim')
        this.countInput = document.getElementById('count')

        document.getElementById('checker').onclick = () => {
            this.validate()
        }

        this.submitter = document.getElementById('submitter')
        this.error = document.getElementById('error')
        this.error.style.display = 'none'

        this.words = words
        this.grid = grid
    }

    validate() {
        if (this._checkIfValid()) {
            const words = this.words.convertIntoList()
            const grid = this.grid.convertIntoAnswer()

            this.wordsInput.value = JSON.stringify(words)
            this.gridInput.value = JSON.stringify(grid)
            this.titleInput.value = document.getElementById('titleInput').value
            this.dimInput.value = String(grid.length)
            this.countInput.value = String(words.length)

            this.submitter.submit()
        }
    }

    _displayError(error = 'Erreur inconnue.') {
        this.error.style.display = 'block'
        this.error.innerText = error
    }

    _checkIfValid() {
        const grid = this.grid.convertIntoAnswer()
        const words = this.words.convertIntoList()
        const dim = grid.length

        if (words.length === 0) {
            this._displayError('Mots manquants')
            return false
        }

        const dummyAnswer = []
        for (let i = 0; i < dim; i++) {
            const l = []
            for (let j = 0; j < dim; j++)
                l.push(false)
            dummyAnswer.push(l)
        }

        for (let word of words) {
            if (word.length <= 0) {
                this._displayError(`Le mot aux coord° (${word.coords.x}, ${word.coords.y}) est vide`)
                return false
            }
            if (!word.definition) {
                this._displayError(`La définition du mot (${word.coords.x}, ${word.coords.y}) est vide`)
                return false
            }
            if (!word.vertical) {
                if ((word.coords.y + word.length) > dim) {
                    this._displayError(
                        `Le mot (${word.coords.x}, ${word.coords.y}) dépasse la grille (${word.coords.y + word.length} > ${dim})`)
                    return false
                }
                for (let y = word.coords.y; y < word.coords.y + word.length; y += 1)
                    dummyAnswer[word.coords.x][y] = true
            } else {
                if ((word.coords.x + word.length) > dim) {
                    this._displayError(
                        `Le mot (${word.coords.x}, ${word.coords.y}) dépasse la grille (${word.coords.x + word.length} > ${dim})`)
                    return false
                }
                for (let x = word.coords.x; x < word.coords.x + word.length; x += 1)
                    dummyAnswer[x][word.coords.y] = true
            }
        }

        for (let x = 0; x < dim; x += 1) {
            for (let y = 0; y < dim; y += 1) {
                if (!((dummyAnswer[x][y] && grid[x][y] !== '') ||
                    (!dummyAnswer[x][y] && grid[x][y] === ''))
                ) {
                    this._displayError(`Coord° (${x}, ${y}) invalid`)
                    return false
                }
            }
        }

        if (document.getElementById('titleInput').value === '') {
            this._displayError('Titre vide')
            return false
        }

        return true
    }
}