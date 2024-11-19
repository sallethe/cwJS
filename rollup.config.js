import typescript from '@rollup/plugin-typescript';

export default {
    input: 'code/src/index.ts', // Entry point
    output: {
        file: 'code/main.mjs',
        format: 'es',
    },
    plugins: [
        typescript({ tsconfig: './tsconfig.json' }),
    ],
};
