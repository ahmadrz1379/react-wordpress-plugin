const path = require('path');

module.exports = {
    entry: './index.js', // Entry point for React code
    output: {
        path: path.resolve(__dirname, 'build'),
        filename: 'index.js', // Output file
    },
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                },
            },
        ],
    },
    resolve: {
        extensions: ['.js', '.jsx'],
    },
    mode: 'production', // Set to 'development' for debugging
};
