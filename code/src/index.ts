import { Grid } from './grid.class'
import { c } from './commons'

const gr = new Grid(10, [
    c(0, 0),
    c(5, 7),
])
gr.generate()