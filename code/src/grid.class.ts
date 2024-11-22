import { Cell } from './cell.class'
import type { Coordinates } from './commons'

const xor = (e1: boolean, e2: boolean) =>
    e1 && !e2 || !e1 && e2

export class Grid {
    matrix: Cell[][]
    dim: number

    constructor(dim: number, disabled: Coordinates[] = []) {
        if (dim < 0) throw new Error('Invalid grid dimension')
        const mtx: Cell[][] = []
        for (let x = 0; x < dim; x += 1) {
            const l: Cell[] = []
            for (let y = 0; y < dim; y += 1) {
                const coords = {
                    x: x,
                    y: y,
                }
                l.push(new Cell(coords, this,
                    disabled.find((e) =>
                        coords.x === e.x && coords.y === e.y) !== undefined))
            }
            mtx.push(l)
        }
        this.matrix = mtx
        this.dim = dim
    }

    generate() {
        const grid = document.getElementById('grid')
        if (!grid) return
        for (const l of this.matrix) {
            const row = document.createElement('div')
            for (const e of l) row.append(e.element)
            grid.appendChild(row)
        }
    }

    selectCell = (coords: Coordinates) => {
        if (coords.x < 0 || coords.y < 0) return
        for (let x = 0; x < this.dim; x += 1)
            for (let y = 0; y < this.dim; y += 1)
                if (xor(x === coords.x, y === coords.y))
                    this.matrix[x][y].highlight()
    }

    unselect = () => {
        for (const row of this.matrix)
            for (const elem of row)
                elem.blur()
    }
}