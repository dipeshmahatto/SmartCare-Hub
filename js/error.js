function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}
function displayError() {
  const errorMessage = getQueryParam("error");
  if (errorMessage) {
    document.getElementById("error-message").textContent =
      decodeURIComponent(errorMessage);
    const url = new URL(window.location);
    url.searchParams.delete("error");
    window.history.replaceState({}, document.title, url.toString());
  }
}
window.onload = displayError;
