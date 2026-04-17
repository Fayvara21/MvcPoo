<link rel="stylesheet" href="/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<h1>Report an Issue</h1>
<div class="container">
    <h1>Report an Issue</h1>
    <form method="POST" action="/incident/create">
        <label>Your Email:</label>
        <input type="email" name="email" required>

        <label>Issue Type:</label>
        <select name="type" required>
            <option value="">Select...</option>
            <option value="bug">Bug</option>
            <option value="crash">Application Crash</option>
            <option value="feature">Feature Request</option>
            <option value="other">Other</option>
        </select>

        <label>Description:</label>
        <textarea name="description" rows="6" required></textarea>

        <label>Steps to Reproduce:</label>
        <textarea name="steps" rows="4"></textarea>

        <button type="submit">Submit Report</button>
    </form>
</div>