class TwitterFetcher {
  constructor() {
    this.uri = `./twitter.php`;
    this.config = { method: 'get' };
  }

  layout(data) {
    for (let item of data) {
      const el = document.createElement('p');
      const text = item.text;
      const author = item.user.screen_name;
      const date = item.created_at;

      el.innerHTML = `${text} <br>posted by ${author} on ${date}`;
      document.body.appendChild(el);
    }
  }

  get() {
    fetch(this.uri, this.config)
      .then(response => response.json())
      .then(data => this.layout(data))
      .catch(error => console.error(error));
  }
}

export default TwitterFetcher;