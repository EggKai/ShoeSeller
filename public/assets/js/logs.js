document.addEventListener('DOMContentLoaded', function() {
  const logContent = document.querySelector('.log-file-contents pre');
  if (logContent) {
    // Define your keywords and corresponding CSS classes.
    const highlights = [
        { regex: /\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/g, className: 'log-time' },
        { regex: /\b(error|warning)\b/gi, className: 'log-warning' },
        { regex: /\bINFO\b/g, className: 'log-info' },
        { regex: /\b(CRITICAL|ALERT)\b/g, className: 'log-alert' },
        { regex: /\bACTION\b/g, className: 'log-action' },
        { regex: /\bid\b/gi, className: 'log-id' },
        { regex: /\b(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\b/g, className: 'log-ip' }
    ];
    
    // Get the text from the pre block
    let content = logContent.innerHTML;

    // Replace each keyword with a <span> containing the same text but with a CSS class
    highlights.forEach(item => {
      content = content.replace(item.regex, match => {
        return `<span class="${item.className}">${match}</span>`;
      });
    });

    // Update the pre block with the colorized text
    logContent.innerHTML = content;
  }
});
