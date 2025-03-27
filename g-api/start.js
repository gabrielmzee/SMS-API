const { exec } = require('child_process');
const concurrently = require('concurrently');

// This will start the PHP server and Vite concurrently
concurrently([
    {
        command: 'php -S localhost:8686 -t public',
        name: 'php-server',
        prefixColor: 'green',
    },
    {
        command: 'vite',
        name: 'vite',
        prefixColor: 'yellow',
    }
]).then(() => {
    console.log('Both services are running!');
}).catch(err => {
    console.error('Error starting services:', err);
});

// Log any output
exec('php -S localhost:8686 -t public', (err, stdout, stderr) => {
    if (err) {
        console.error(`Error executing PHP server: ${err}`);
        return;
    }
    if (stderr) {
        console.error(`PHP server stderr: ${stderr}`);
        return;
    }
    console.log(`PHP server stdout: ${stdout}`);
});

// Example to add a delay before exiting
setTimeout(() => {
    console.log('Closing application after 5 seconds...');
    process.exit(0); // This will close the app after 5 seconds
}, 5000);

