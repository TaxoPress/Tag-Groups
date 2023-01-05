import apiFetch from '@wordpress/api-fetch';
const { __ } = wp.i18n;

/**
 * Loading Groups
 */
export function getGroupsFromApi(_this) {
  // retrieve the groups
  apiFetch({ path: _this.groupsEndPoint })
    .then((groups) => {
      if (groups) {
        _this.setState({ groups });
      }
    })
    .catch((error) => {
      if (_this.isStillMounted && fetchRequest === _this.currentFetchRequest) {
        _this.setState({
          response: {
            error: true,
            errorMsg: error.message,
          },
        });
      }
    });
}

/**
 * Loading Taxonomies (own REST API endpoint)
 */
export function getTaxonomiesFromApi(_this) {
  // retrieve the taxonomies
  apiFetch({ path: _this.taxonomiesEndPoint })
    .then((taxonomies) => {
      if (taxonomies) {
        _this.setState({ taxonomies });
      }
    })
    .catch((error) => {
      if (_this.isStillMounted && fetchRequest === _this.currentFetchRequest) {
        _this.setState({
          response: {
            error: true,
            errorMsg: error.message,
          },
        });
      }
    });
}

/**
 * Loading Posts
 */
export function getPostsFromApi(_this) {
  apiFetch({ path: '/wp/v2/posts?per_page=26' })
    .then((fullPosts) => {
      if (fullPosts) {
        let posts = [
          { value: -1, label: __('off') },
          { value: 0, label: __('[use this post]') },
        ];
        fullPosts.forEach((fullPost) => {
          posts.push({
            value: fullPost.id,
            label: htmlDecode(fullPost.title.rendered),
          });
        });
        if (posts[27] !== undefined) {
          posts[27] = {
            value: -2,
            label: __('[list cut off after 25 posts]'),
          };
        }
        _this.setState({ posts });
      }
    })
    .catch((error) => {
      if (_this.isStillMounted && fetchRequest === _this.currentFetchRequest) {
        _this.setState({
          response: {
            error: true,
            errorMsg: error.message,
          },
        });
      }
    });
}

function htmlDecode(input) {
  var e = document.createElement('textarea');
  e.innerHTML = input;
  return e.childNodes.length === 0 ? '' : e.childNodes[0].nodeValue;
}
