/**
 *   Last modified: 2022/06/28 20:19:07
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL3
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

var TagGroupsTogglePostFilter = {
  lastArticle: 0,
  masonryElement: null,
  paged: 0,
  sliderMarginLeft: null,
  sliderMarginRight: null,
  sliderWidth: null,
  sliderisOpen: false,
  tagSearchTimer: null,
  textSearchLastVal: '',
  textSearchTimer: null,
  textSearchVal: '',
  timestamp: 0,
  totalPages: 0,
  postponedTermSearches: 0,
  postponedTextSearches: 0,
  isSliderEngaged: false,

  load: function (options) {
    this._o = options;
    if (!this.loadBodyVariables()) {
      return;
    }

    this._o.accordion = this._o.accordion || false;
    this._o.ajaxLink = this._o.ajaxLink || '';
    this._o.cacheKey = this._o.cacheKey || null;
    this._o.cachingTime = this._o.cachingTime || 0;
    this._o.debug = this._o.debug || null;
    this._o.defaultImageSrc = this._o.defaultImageSrc || '';
    this._o.defaultShowPosts = this._o.defaultShowPosts || false;
    this._o.displayAmount = this._o.displayAmount || 0;
    this._o.divId = this._o.divId || '';
    this._o.groupIds = this._o.groupIds || [];
    this._o.legacyMenu = this._o.legacyMenu || false;
    this._o.messageAmountPl = this._o.messageAmountPl || '';
    this._o.messageAmountSg = this._o.messageAmountSg || '';
    this._o.messageGoBack = this._o.messageGoBack || '';
    this._o.messageLoadMore = this._o.messageLoadMore || '';
    this._o.messageNothingFound = this._o.messageNothingFound || '';
    this._o.operator = this._o.operator || '';
    this._o.order = this._o.order || '';
    this._o.orderBy = this._o.orderBy || '';
    this._o.pager = this._o.pager || false;
    this._o.persistentFilter = this._o.persistentFilter || 0;
    this._o.postsPerPage = this._o.postsPerPage || 0;
    this._o.postsPlaceholder = this._o.postsPlaceholder || '';
    this._o.presetTermSlugs = this._o.presetTermSlugs || [];
    this._o.staticTaxonomy = this._o.staticTaxonomy || '';
    this._o.staticTerms = this._o.staticTerms || '';
    this._o.taxonomy = this._o.taxonomy || '';
    this._o.template = this._o.template || '';
    this._o.textSearch = this._o.textSearch || 0;
    this._o.transition = this._o.transition || '';
    this._o.pagerPosition = this._o.pagerPosition || 'bottom';
    this._o.layout = this._o.layout || 'classic';
    this._o.timeout = this._o.timeout || 1000;

    const _this = this;

    if (this._o.isSlider) {
      this.sliderElement = document.getElementById(this._o.divId);
    }

    if (typeof tagGroupsSliderSide !== 'undefined') {
      if ('left' === tagGroupsSliderSide) {
        this.sliderLeftListeners();
      }
      if ('right' === tagGroupsSliderSide) {
        this.sliderRightListeners();
      }
    }

    this.viewPortWidth = Math.max(
      document.documentElement.clientWidth,
      window.innerWidth || 0
    );

    this.getPresettags();
    if (this._o.persistentFilter) {
      // restore the persistent filter from the cookie
      this.cookieLoadFilter();
      this.beforeLoadingPosts();
    } else {
      var presetsLoaded = this.loadPresets();
      this.beforeLoadingPosts();
      if (presetsLoaded) {
        this.retrievePosts(true); // don't save settings to cache
      } else if (this._o.defaultShowPosts) {
        this.retrievePosts(true, true); // don't save settings to cache and don't show noty
      } else {
        this.retrievePostsConditionally();
      }
    }

    // Prevent browsers to scroll where they like, if they execute JS
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }

    jQuery('#tg_filter_dpf_toggle_box_posts').html(this._o.postsPlaceholder);

    /* Reload the posts if the filter was changed - triggered by input
    (keep so customizations can access toggle)
    */
    jQuery('.tg_group_dpf_toggle_term').on('change', () => {
      if (this._o.persistentFilter) {
        this.setCookie('taggroupstpfterms', '', 0);
        this.setCookie('taggroupstpfpaged', 0, 0);
        this.setCookie('taggroupstpflastarticle', 0, 0);
        this.setCookie('taggroupstpforder', '', 0);
        this.setCookie('taggroupstpforderby', '', 0);
      }
      this.paged = 0;
      // Wait for 1 second since we might still be busy with toggles
      clearTimeout(this.tagSearchTimer);
      this.postponedTermSearches++;
      // dynamically increase the timeout for people who click a lot
      let timeoutIncrease = 0;
      if (this.postponedTermSearches > 4) {
        timeoutIncrease = 2000;
      } else if (this.postponedTermSearches > 2) {
        timeoutIncrease = 1000;
      }
      this.tagSearchTimer = setTimeout(() => {
        this.retrievePosts();
      }, this._o.timeout + timeoutIncrease);
    });

    /* Reload the posts if the filter was changed - triggered by div */
    jQuery('.tg_tpf_trigger').on('click', function (event) {
      if (jQuery(event.target).is('input:checkbox')) {
        return;
      }
      const groupId = jQuery(event.target)
        .closest('.tg_tpf_trigger')
        .attr('data-groupid');
      const termId = jQuery(event.target)
        .closest('.tg_tpf_trigger')
        .attr('data-termid');
      var toggle = jQuery(
        '#tg_group_dpf_toggle_term_' + groupId + '_' + termId
      );
      toggle.prop('checked', !toggle.prop('checked'));
      toggle.trigger('change'); // hook into change event to keep checkboxes relevant
    });

    if (this._o.persistentFilter) {
      jQuery('body').on('click', 'article.tg_dpf_article a', function (e) {
        _this.lastArticle = jQuery(this).closest('article').attr('id');
        _this.setCookie(
          'taggroupstpflastarticle',
          _this.lastArticle,
          _this._o.persistentFilter
        );
        _this.cookieSaveFilter();

        const order = jQuery('.tg_tpf_order_select').val() || _this._o.order;
        const orderBy =
          jQuery('.tg_tpf_orderby_select').val() || _this._o.orderBy;
        _this.setCookie('taggroupstpforder', order, _this._o.persistentFilter);
        _this.setCookie(
          'taggroupstpforderby',
          orderBy,
          _this._o.persistentFilter
        );
      });
    }

    jQuery('body').on(
      'change',
      '.tg_tpf_orderby_select,.tg_tpf_order_select',
      () => {
        this.paged = 0;
        this.retrievePosts();
      }
    );

    /* clicked the pager up */
    jQuery('body').on(
      'click',
      '#tg_tpf_pager_wrapper_top #tg_pager_up, #tg_tpf_pager_wrapper_bottom #tg_pager_up',
      function () {
        _this.paged++;
        _this.retrievePosts();
        _this.scrollToPostsIfBottomPager(this);
      }
    );

    /* clicked the pager down */
    jQuery('body').on(
      'click',
      '#tg_tpf_pager_wrapper_top #tg_pager_down, #tg_tpf_pager_wrapper_bottom #tg_pager_down',
      function () {
        _this.paged--;
        _this.retrievePosts();
        _this.scrollToPostsIfBottomPager(this);
      }
    );

    /* going to previous or next page with cursors */
    jQuery('body').on('keydown', function (event) {
      if (event.which === 37 && _this.paged > 0) {
        _this.paged--;
        _this.retrievePosts();
        _this.scrollToPostsIfBottomPager(this);
      } else if (event.which === 39 && _this.paged < _this.totalPages - 1) {
        _this.paged++;
        _this.retrievePosts();
        _this.scrollToPostsIfBottomPager(this);
      } else if (event.which === 27 && _this.sliderisOpen) {
        if ('left' === tagGroupsSliderSide) {
          _this.closeLeftSlider();
        } else if ('right' === tagGroupsSliderSide) {
          _this.closeRightSlider();
        }
      }
    });

    /* clicked on a pager number */
    jQuery('#tag_groups_dpf_toggle_body').on(
      'click',
      '.tg_pager_number',
      function () {
        var newPage = jQuery(this).attr('data-page');
        if (newPage) {
          _this.paged = newPage - 1;
          _this.retrievePosts();
          _this.scrollToPostsIfBottomPager(this);
        }
      }
    );

    jQuery('.tg_dpf_toggle_reset_button').on('click', () => {
      jQuery('.tg_group_dpf_toggle_term').prop('checked', false);
      jQuery('.tg_tpf_text_search_trigger').val('');
      this.textSearchVal = '';
      this.paged = 0;
      if (this._o.persistentFilter) {
        this.setCookie('taggroupstpfterms', '', 0);
        this.setCookie('taggroupstpfpaged', 0, 0);
        this.setCookie('taggroupstpflastarticle', 0, 0);
        this.setCookie('taggroupstpforder', '', 0);
        this.setCookie('taggroupstpforderby', '', 0);
      }
      this.loadPresets();
      this.beforeLoadingPosts();
      this.retrievePosts();
    });

    if (this._o.accordion) {
      this.createAccordion(this._o.accordion);
    }

    if (typeof tagGroupsSeparateTextSearch !== 'undefined') {
      this._o.textSearch = Math.max(
        tagGroupsSeparateTextSearch,
        this._o.textSearch
      );
    }

    if (this._o.textSearch) {
      jQuery('.tg_tpf_text_search_trigger').on('keyup', (e) => {
        this.textSearchVal = jQuery(e.target).val();
        // update the other text fields
        jQuery('.tg_tpf_text_search_trigger')
          .not(e.target)
          .val(this.textSearchVal);
        if (e.which === 13) {
          this.retrievePosts();
          return false;
        }
        if (this._o.textSearch === 2) {
          if (this.textSearchLastVal !== this.textSearchVal) {
            clearTimeout(this.textSearchTimer);
            this.postponedTextSearches++;
            // dynamically increase the timeout for people who type a lot
            let timeoutIncrease = 0;
            if (this.postponedTextSearches > 20) {
              timeoutIncrease = 2000;
            } else if (this.postponedTextSearches > 10) {
              timeoutIncrease = 1000;
            }
            this.textSearchLastVal = this.textSearchVal;
            this.textSearchTimer = setTimeout(() => {
              this.paged = 0;
              this.retrievePosts();
            }, this._o.timeout + timeoutIncrease);
            return false;
          }
        }
      });
    }

    this.adjustSliderHeight();

    // regularly adjust slider height because on mobile screens the browser menu might appear and disappear
    setInterval(() => {
      this.adjustSliderHeight();
    }, 5000);
  },

  /**
   * retrieve posts if browser loaded page with anything toggled on
   */
  retrievePostsConditionally: function () {
    if (
      jQuery('.tg_tpf_text_search_trigger') &&
      jQuery('.tg_tpf_text_search_trigger:first').val()
    ) {
      this.retrievePosts();
    } else {
      if (
        this._o.groupIds.some((groupId) => {
          return jQuery('[data-groupid=' + groupId + ']:checked').length;
        })
      ) {
        this.retrievePosts();
      }
    }
  },

  getPresettags: function () {
    var presettagsGet = this.getUrlParameter('presettags');
    if (presettagsGet) {
      this._o.presetTermSlugs = presettagsGet
        .toLowerCase()
        .replace(' ', '')
        .split(',');
    }
  },

  // https://davidwalsh.name/query-string-javascript
  getUrlParameter: function (name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null
      ? ''
      : decodeURIComponent(results[1].replace(/\+/g, ' '));
  },

  createAccordion: function (mode) {
    let options = {
      collapsible: true,
      heightStyle: 'content',
      active: false,
      animate: 800,
    };

    if (mode > 1) {
      options['event'] = 'mouseover';
    }
    jQuery('.tg_group_dpf_toggle_group_container').accordion(options);
    // open groups that contain checked tags
    this._o.groupIds.forEach((groupId) => {
      // if (groupId > -1) {
      jQuery('[data-groupid=' + groupId + ']:checked').each(() => {
        jQuery('#tg_group_dpf_toggle_group_container_' + groupId).accordion(
          'option',
          'active',
          0
        );
      });
      // }
    });
  },

  /**
   * Retrieve the posts
   */
  retrievePosts: function (dontSaveFilter, suppressNoty) {
    this.postponedTermSearches = 0;
    this.postponedTextSearches = 0;
    var terms = [];
    var termsFound = false;
    var _this = this;
    this._o.groupIds.forEach((groupId) => {
      // if (groupId > 0) {
      var group = {};
      group.groupid = groupId;
      group.termids = [];
      jQuery('[data-groupid=' + groupId + ']:checked').each((i, el) => {
        group.termids.push(parseInt(jQuery(el).attr('data-termid')));
        termsFound = true;
      });
      terms.push(group);
      // }
    });
    if (!this.textSearchVal && !termsFound && !this._o.defaultShowPosts) {
      if (this.isMasonry() && this.masonryElement) {
        this.masonryElement.masonry('remove', jQuery('.tg_dpf_article'));
      }
      if (this._o.transition === 'fade') {
        jQuery('#tg_filter_dpf_toggle_box_posts').fadeTo(500, 0, function () {
          jQuery(this)
            .html(_this._o.postsPlaceholder)
            .promise()
            .done(function () {
              jQuery(this).fadeTo(500, 1);
            });
        });
      } else {
        jQuery('#tg_filter_dpf_toggle_box_posts').html(
          this._o.postsPlaceholder
        );
        jQuery('#tg_filter_dpf_toggle_box_posts').removeClass('tg_opacity');
      }

      this.renderMessage('');

      jQuery('#tg_tpf_pager_wrapper_top').html('').hide();
      jQuery('#tg_tpf_pager_wrapper_bottom').html('').hide();

      this.totalPages = 0;

      return;
    }

    if (!this.textSearchVal && !termsFound && this._o.defaultShowPosts) {
      suppressNoty = true;
    }

    if (this._o.persistentFilter && !dontSaveFilter) {
      this.cookieSaveFilter();
    }

    // Update the timestamp
    this.timestamp = Date.now();

    // fade list of posts to signify that something will change
    if (this._o.transition === 'fade') {
      jQuery('#tg_filter_dpf_toggle_box_posts').fadeTo(500, 0.5);
      jQuery('#tg_tpf_pager_wrapper_top').fadeTo(500, 0.5);
      jQuery('#tg_tpf_pager_wrapper_bottom').fadeTo(500, 0.5);
    } else {
      jQuery('#tg_filter_dpf_toggle_box_posts').addClass('tg_opacity');
      jQuery('#tg_tpf_pager_wrapper_top').addClass('tg_opacity');
      jQuery('#tg_tpf_pager_wrapper_bottom').addClass('tg_opacity');
    }
    if (this._o.textSearch) {
      this.textSearchVal = jQuery('.tg_tpf_text_search_trigger:first').val();
    }

    const order = jQuery('.tg_tpf_order_select').val() || this._o.order;
    const orderBy = jQuery('.tg_tpf_orderby_select').val() || this._o.orderBy;

    jQuery.ajax({
      url: this._o.ajaxLink,
      dataType: 'text',
      data: {
        action: 'tg_ajax_tpf_get_posts',
        caching_time: this._o.cachingTime,
        count_total: this._o.displayAmount || this._o.pager,
        default_image_src: this._o.defaultImageSrc,
        operator: this._o.operator,
        order: order,
        orderby: orderBy,
        paged: this.paged,
        posts_per_page: this._o.postsPerPage,
        s: this.textSearchVal,
        static_taxonomy: this._o.staticTaxonomy,
        static_terms: this._o.staticTerms,
        taxonomy: this._o.taxonomy,
        template: this._o.template,
        terms: terms,
        timestamp: this.timestamp,
      },
      method: 'post',
      success: (rawData) => {
        try {
          // strip anything before the first opening {
          rawData = rawData.replace(/[^{]*{/, '{');
          var data = JSON.parse(rawData.trim());
        } catch (e) {
          console.log(
            '[Tag Groups] Error parsing data from server',
            e.message,
            ', data:"' + rawData.toString() + '"'
          );
          return false;
        }
        var timestamp = data.timestamp;
        /* check timestamp to make sure that we have received the respons to the LATEST request */
        if (this.timestamp > timestamp) {
          // We are expecting something newer
          if (this._o.debug) {
            console.log(
              '[Tag Groups] Discarding data, expecting response to later request.'
            );
          }
          return;
        }

        if (this._o.transition === 'fade') {
          jQuery('#tg_filter_dpf_toggle_box_posts').fadeTo(500, 0);
          jQuery('#tg_filter_dpf_toggle_box_posts').html('');
        } else {
          jQuery('#tg_filter_dpf_toggle_box_posts').html('');
        }

        var posts = data.posts;
        var pager = data.pager;
        var totalCount = data.count || 0;

        /* compiling the post output */
        var poststxt = '';
        var pagertxt = '';
        var postCountForDisplay;

        if (this._o.pager) {
          postCountForDisplay = totalCount;
        } else {
          postCountForDisplay = posts.length;
        }

        this.totalPages = pager.total_pages;

        if (posts.length == 0) {
          this.paged = 0;
          pager.down = false;
          pager.up = false;
          poststxt +=
            '<div class="tg_dpf_nothing_found">' +
            this._o.messageNothingFound +
            '</div>';
        } else {
          poststxt += '<div class="tg-grid-sizer"></div>';
          posts.forEach((post, i) => {
            poststxt +=
              '<article id="' +
              post.id +
              `" class="tg_dpf_article tg-article-count-${i + 1}">`;
            poststxt += post.content;
            poststxt += '</article>';
          });
        }

        if (
          (true === this._o.pager || 1 === this._o.pager) &&
          (pager.up || pager.down)
        ) {
          // simple pager: previous and next
          pagertxt +=
            '<nav role="navigation" aria-label="Pagination Navigation"><h4 class="tg_pager">';
          if (pager.down) {
            pagertxt +=
              '<span id="tg_pager_down" class="tg_dpf_back tg_pointer tg_left" aria-label="previous"><span class="dashicons dashicons-arrow-left-alt"></span>&nbsp;' +
              this._o.messageGoBack +
              '</span> ';
          }
          if (pager.up) {
            pagertxt +=
              ' <span id="tg_pager_up" class="tg_dpf_more tg_pointer tg_right" aria-label="next">' +
              this._o.messageLoadMore +
              '&nbsp;<span class="dashicons dashicons-arrow-right-alt"></span></span> ';
          }
          pagertxt += '</h4></nav>';
        } else if (2 === this._o.pager && pager.total_pages > 1) {
          // pager with arrows and pages
          pagertxt +=
            '<nav role="navigation" aria-label="Pagination Navigation"><div class="tg_pager tg_pager_pages">';
          if (pager.page > 1) {
            pagertxt += `<div class="tg_pager_number tg_pointer" data-page="${
              pager.page - 1
            }" aria-label="previous">&lt;</div>`;
          }
          var dotsBelowSet = false;
          var dotsAboveSet = false;
          if (pager.total_pages > 1) {
            for (var i = 1; i <= pager.total_pages; i++) {
              if (
                pager.total_pages < 6 ||
                Math.abs(pager.page - i) < 3 ||
                i === 1 ||
                i === pager.total_pages
              ) {
                if (i === pager.page) {
                  pagertxt += `<div class="tg_pager_number tg_pager_number_active" aria-label="Current Page, Page ${i}" aria-current="true">${i}</div>`;
                } else {
                  pagertxt += `<div class="tg_pager_number tg_pointer" data-page="${i}" aria-label="Goto Page ${i}">${i}</div>`;
                }
              } else if (i < pager.page && !dotsBelowSet) {
                pagertxt += `<div class="tg_pager_number tg_pager_number_inactive" >...</div>`;
                dotsBelowSet = true;
              } else if (i > pager.page && !dotsAboveSet) {
                pagertxt += `<div class="tg_pager_number tg_pager_number_inactive" >...</div>`;
                dotsAboveSet = true;
              }
            }
          } else {
            pagertxt += `<div class="tg_pager_number tg_pager_number_active" aria-label="Current Page, Page ${pager.page}" aria-current="true">${pager.page}</div>`;
          }
          if (pager.page < pager.total_pages) {
            pagertxt += `<div class="tg_pager_number tg_pointer" data-page="${
              +pager.page + 1
            }" aria-label="next">&gt;</div>`;
          }
          pagertxt += '</div></nav>';
        }

        if (this._o.displayAmount) {
          if (postCountForDisplay > 0) {
            if (postCountForDisplay == 1) {
              var count_message = this._o.messageAmountSg;
            } else {
              var count_message = this._o.messageAmountPl.replace(
                new RegExp('\\{count}', 'gm'),
                postCountForDisplay
              );
            }
            amounttxt =
              '<div id="tg_filter_box_amount">' + count_message + '</div>';

            this.renderMessage(amounttxt);
            if (this._o.displayAmount === 2 && !suppressNoty) {
              jQuery.jnoty(count_message, {
                theme: 'jnoty-success',
                click: () => {
                  this.closeSliderScrollToPosts();
                  return false;
                },
                afterOpen: () => {
                  jQuery('.jnoty-message').attr('role', 'alert');
                },
              });
            }
          } else {
            if (this._o.displayAmount === 2 && !suppressNoty) {
              jQuery.jnoty(this._o.messageNothingFound, {
                theme: 'jnoty-info',
                click: () => {
                  this.closeSliderScrollToPosts();
                  return false;
                },
                afterOpen: () => {
                  jQuery('.jnoty-message').attr('role', 'alert');
                },
              });
            }

            this.renderMessage('');
          }
        }

        const _this = this;

        if (this._o.transition === 'fade') {
          jQuery('#tg_filter_dpf_toggle_box_posts').fadeTo(200, 0, function () {
            const postBox = '#tg_filter_dpf_toggle_box_posts';
            if (_this.isMasonry() && this.masonryElement) {
              jQuery(postBox).css('overflow', 'hidden');
              jQuery(postBox).css('opacity', '0');
              jQuery(postBox).css('display', 'none');
              _this.masonryElement.masonry('remove', jQuery('.tg_dpf_article'));
            }
            jQuery(postBox)
              .html(poststxt)
              .promise()
              .done(function () {
                jQuery(postBox).css('display', 'block');
                if (posts.length) {
                  if (_this.isMasonry()) {
                    _this.applyMasonry(postBox, true);
                  } else {
                    jQuery(postBox).fadeTo(500, 1, function () {
                      setTimeout(() => {
                        _this.scrollToArticle();
                      }, 500);
                    });
                  }
                } else {
                  jQuery(postBox).fadeTo(500, 1);
                }
              });
          });
          if (pagertxt) {
            if (
              'top' === this._o.pagerPosition ||
              'both' === this._o.pagerPosition
            ) {
              jQuery('#tg_tpf_pager_wrapper_top')
                .show()
                .fadeTo(200, 0, function () {
                  jQuery(this)
                    .html(pagertxt)
                    .promise()
                    .done(function () {
                      jQuery(this).fadeTo(500, 1);
                    });
                });
            }
            if (
              'bottom' === this._o.pagerPosition ||
              'both' === this._o.pagerPosition
            ) {
              jQuery('#tg_tpf_pager_wrapper_bottom')
                .show()
                .fadeTo(200, 0, function () {
                  jQuery(this)
                    .html(pagertxt)
                    .promise()
                    .done(function () {
                      jQuery(this).fadeTo(500, 1);
                    });
                });
            }
          } else {
            jQuery('#tg_tpf_pager_wrapper_top').html('').hide();
            jQuery('#tg_tpf_pager_wrapper_bottom').html('').hide();
          }
        } else {
          if (this.isMasonry() && this.masonryElement) {
            jQuery('#tg_filter_dpf_toggle_box_posts').css('overflow', 'hidden');
            this.masonryElement.masonry('remove', jQuery('.tg_dpf_article'));
          }
          jQuery('#tg_filter_dpf_toggle_box_posts').removeClass('tg_opacity');
          jQuery('#tg_filter_dpf_toggle_box_posts')
            .show()
            .html(poststxt)
            .promise()
            .done(function () {
              if (posts.length) {
                if (_this.isMasonry()) {
                  _this.applyMasonry(this, false);
                } else {
                  setTimeout(() => {
                    _this.scrollToArticle();
                  }, 500);
                }
              }
            });
          if (pagertxt) {
            if (
              'top' === this._o.pagerPosition ||
              'both' === this._o.pagerPosition
            ) {
              jQuery('#tg_tpf_pager_wrapper_top')
                .show()
                .removeClass('tg_opacity');
              jQuery('#tg_tpf_pager_wrapper_top').show().html(pagertxt);
            }
            if (
              'bottom' === this._o.pagerPosition ||
              'both' === this._o.pagerPosition
            ) {
              jQuery('#tg_tpf_pager_wrapper_bottom')
                .show()
                .removeClass('tg_opacity');
              jQuery('#tg_tpf_pager_wrapper_bottom').show().html(pagertxt);
            }
          } else {
            jQuery('#tg_tpf_pager_wrapper_top').html('').hide();
            jQuery('#tg_tpf_pager_wrapper_bottom').html('').hide();
          }
        }
        this.afterLoadingPosts();
      },
      error: (xhr, textStatus, errorThrown) => {
        console.log(
          '[Tag Groups] error (retrievePosts): ' + xhr.responseText
        );
      },
    });
  },

  /**
   *
   * @param {string} message
   */
  renderMessage: function (message) {
    if (this._o.transition === 'fade') {
      jQuery('#tg_filter_dpf_toggle_box_messages').fadeTo(500, 0, function () {
        jQuery(this)
          .html('<div id="tg_filter_box_amount">' + message + '</div>')
          .promise()
          .done(function () {
            jQuery(this).fadeTo(500, 1);
          });
      });
    } else {
      jQuery('#tg_filter_dpf_toggle_box_messages').html(
        '<div id="tg_filter_box_amount">' + message + '</div>'
      );
      jQuery('#tg_filter_dpf_toggle_box_messages').css('opacity', 1);
    }
  },

  /**
   * Apply the masonry layout
   * @param {object} element
   */
  applyMasonry: function (element, fade) {
    if (!this.masonryElement) {
      this.masonryElement = jQuery(element).masonry({
        itemSelector: '.tg_dpf_article',
        percentPosition: true,
        initLayout: false, // we need to wait for images with imagesLoaded() before doing layout
        // transitionDuration: '1.5s',
        columnWidth: '.tg-grid-sizer',
      });
    } else {
      this.masonryElement.masonry('addItems', jQuery('.tg_dpf_article'));
    }
    this.masonryElement.masonry('on', 'layoutComplete', () => {
      this.scrollToArticle();
    });
    if (typeof this.masonryElement.imagesLoaded === 'function') {
      this.masonryElement.imagesLoaded(() => {
        this.masonryElement.masonry('layout');

        jQuery('#tg_filter_dpf_toggle_box_posts').css('overflow', 'none');
        if (fade) {
          jQuery('#tg_filter_dpf_toggle_box_posts').fadeTo(500, 1);
        } else {
          jQuery('#tg_filter_dpf_toggle_box_posts').css('opacity', '1');
        }
      });
    } else {
      if (this._o.debug) {
        console.log(
          '[Tag Groups] imagesLoaded() not found! Please check that all scripts are loaded correctly'
        );
      }
      setTimeout(() => {
        this.masonryElement.masonry('layout');

        jQuery('#tg_filter_dpf_toggle_box_posts').css('overflow', 'none');
        if (fade) {
          jQuery('#tg_filter_dpf_toggle_box_posts').fadeTo(500, 1);
        } else {
          jQuery('#tg_filter_dpf_toggle_box_posts').css('opacity', '1');
        }
      }, 1000);
    }
  },

  /**
   * Conditionally scroll to the article
   */
  scrollToArticle: function () {
    // scroll to the group selectors, in order to avoid occasional random positions
    if (this._o.persistentFilter && this.lastArticle > 0) {
      if (
        typeof jQuery(
          'article#' + this.lastArticle + '.tg_dpf_article'
        ).offset() !== 'undefined'
      ) {
        const newTop =
          jQuery('article#' + this.lastArticle + '.tg_dpf_article').offset()
            .top - 120; // define here to make sure we don't change it during timeout
        setTimeout(() => {
          // delay to make sure the posts are rendered
          jQuery('html, body')
            .stop()
            .animate({ scrollTop: newTop }, 500, 'swing');

          // We need to reset the lastArticle
          this.setCookie('taggroupstpflastarticle', 0, 0);
          this.lastArticle = 0;
        }, 500);
      }
    } else {
      if (this.viewPortWidth < 1000) {
        // Don't scroll on big screens. We measure the width because a narrow screen makes the menu stack vertically
        this.scrollToPosts();
      }
    }
  },

  scrollToPosts: function () {
    if (
      typeof jQuery('#tg_filter_dpf_toggle_box_posts').offset() !== 'undefined'
    ) {
      var newTop = jQuery('#tg_filter_dpf_toggle_box_posts').offset().top - 120;
      jQuery('html, body').stop().animate({ scrollTop: newTop }, 500, 'swing');
    }
  },

  closeSliderScrollToPosts: function () {
    if (this.sliderisOpen) {
      if (typeof tagGroupsSliderSide !== 'undefined') {
        this.getDimensions();
        if (tagGroupsSliderSide === 'left') {
          this.closeLeftSlider();
        } else {
          this.closeRightSlider();
        }
      }
    }
    this.scrollToPosts();
  },

  loadPresets: function () {
    var affectedItems = 0;
    if (this._o.presetTermSlugs.length) {
      this._o.presetTermSlugs.forEach((slug) => {
        var ret = jQuery('[data-slug="' + slug).prop('checked', true);
        affectedItems += ret.length;
      });
    }
    return affectedItems;
  },

  /**
   * read the cookie that saves the filter state
   */
  cookieLoadFilter: function () {
    var tagFilter = this.getCookie('taggroupstpfterms');
    this.textSearchVal = this.getCookie('taggroupstpftextsearch');
    var order = this.getCookie('taggroupstpforder');
    var orderBy = this.getCookie('taggroupstpforderby');
    if (order) {
      jQuery('.tg_tpf_order_select').val(order);
    }
    if (orderBy) {
      jQuery('.tg_tpf_orderby_select').val(orderBy);
    }

    if (tagFilter === '' && this.textSearchVal === '') {
      var presetsLoaded = this.loadPresets();
      if (presetsLoaded) {
        this.retrievePosts(true); // don't save settings to cache
      } else if (this._o.defaultShowPosts) {
        this.retrievePosts(true, true); // don't save settings to cache and don't show noty
      } else {
        this.retrievePostsConditionally();
      }
    } else {
      if (this.textSearchVal) {
        jQuery('.tg_tpf_text_search_trigger').val(this.textSearchVal);
      }

      if (tagFilter) {
        jQuery('.tg_group_dpf_toggle_term').removeAttr('checked');
        try {
          var groupsTerms = JSON.parse(tagFilter);
        } catch (e) {
          console.log(
            '[Tag Groups] Error parsing data from cookie',
            e.message,
            ', data:"' + tagFilter + '"'
          );
          return false;
        }
        for (var i in groupsTerms) {
          if (groupsTerms.hasOwnProperty(i) && groupsTerms[i]) {
            groupsTerms[i].t.forEach((termid) => {
              jQuery(
                '[data-groupid="' +
                  groupsTerms[i].g +
                  '"][data-termid="' +
                  termid +
                  '"]'
              ).prop('checked', true);
            });
          }
        }
      }

      this.paged = this.getCookie('taggroupstpfpaged');
      this.retrievePosts();
    }
    this.lastArticle = this.getCookie('taggroupstpflastarticle');
  },

  cookieSaveFilter: function () {
    var terms = [];
    this._o.groupIds.forEach((groupId) => {
      if (groupId > 0) {
        var group = {};
        group.g = groupId;
        group.t = [];
        jQuery('[data-groupid=' + groupId + ']:checked').each(function () {
          group.t.push(parseInt(jQuery(this).attr('data-termid')));
        });
        terms.push(group);
      }
    });
    var jsonTerms = JSON.stringify(terms);
    this.setCookie('taggroupstpfterms', jsonTerms, this._o.persistentFilter);
    this.setCookie('taggroupstpfpaged', this.paged, this._o.persistentFilter);
    if (typeof this.textSearchVal !== 'undefined') {
      this.setCookie(
        'taggroupstpftextsearch',
        this.textSearchVal,
        this._o.persistentFilter
      );
    }
  },

  /**
   * https://www.w3schools.com/js/js_cookies.asp
   */
  setCookie: function (cname, cvalue, exmins) {
    if (!this._o.persistentFilter) {
      return;
    }
    var d = new Date();
    d.setTime(d.getTime() + exmins * 60 * 1000);
    var expires = 'expires=' + d.toUTCString();
    document.cookie =
      cname +
      '-' +
      this._o.cacheKey +
      '=' +
      encodeURIComponent(cvalue) +
      ';' +
      expires +
      ';path=/; SameSite=Strict';
  },

  /**
   * https://www.w3schools.com/js/js_cookies.asp
   */
  getCookie: function (cname) {
    if (!this._o.persistentFilter) {
      return;
    }

    var name = cname + '-' + this._o.cacheKey + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      try {
        var c = decodeURIComponent(ca[i]);
      } catch (error) {
        continue;
      }
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return '';
  },

  isMasonry: function () {
    return (
      this._o.layout === 'masonry' ||
      this._o.layout === 'masonry-small' ||
      this._o.layout === 'masonry-large'
    );
  },

  loadBodyVariables: function () {
    // legacy menu
    if (typeof this._o.legacyMenu !== 'undefined' && this._o.legacyMenu) {
      return true;
    }

    if (typeof tagGroupsTPFBodyOptions === 'undefined') {
      console.log(
        '[Tag Groups] Body (posts) part of Toggle Post Filter is missing'
      );
      return false;
    }

    // legacy body
    if (
      typeof tagGroupsTPFBodyOptions.legacyBody !== 'undefined' &&
      tagGroupsTPFBodyOptions.legacyBody
    ) {
      return true;
    }

    // body parts have priority
    this._o = { ...this._o, ...tagGroupsTPFBodyOptions };

    return true;
  },

  beforeLoadingPosts: function () {
    if (typeof tagGroupsTPFAllButtonsSetColor !== 'undefined') {
      tagGroupsTPFAllButtonsSetColor();
    }

    if (typeof tagGroupsTPFAllTagsSetTagState !== 'undefined') {
      tagGroupsTPFAllTagsSetTagState();
    }
  },

  afterLoadingPosts: function () {
    setTimeout(() => {
      var bodyPart = document.getElementById('tag_groups_dpf_toggle_body');
      if (bodyPart) {
        var computedStyle = window.getComputedStyle(bodyPart, null);
        bodyPart.style.minHeight = computedStyle.height;
      }
    }, 2000); // wait for images
  },

  scrollToPostsIfBottomPager: function (el) {
    if (jQuery(el).parents('#tg_tpf_pager_wrapper_bottom').length) {
      this.scrollToPosts();
    }
  },

  /**
   * Sliders
   */
  adjustSliderHeight: function () {
    if (typeof this.sliderElement !== 'undefined') {
      const viewPortHeight = Math.max(
        document.documentElement.clientHeight,
        window.innerHeight || 0
      );
      this.sliderElement.style.setProperty(
        'height',
        viewPortHeight + 'px',
        'important'
      );
    }
  },

  /**
   * slider on the left side
   */
  toggleLeftSlider: function () {
    this.getDimensions();
    if (this.sliderMarginLeft < -1 * this.sliderWidth) {
      this.openLeftSlider();
    } else {
      this.closeLeftSlider();
    }
  },

  openLeftSlider: function () {
    if (this.sliderElement === undefined || this.isSliderEngaged) {
      return false;
    }
    this.isSliderEngaged = true;
    this.sliderElement.style.setProperty(
      'margin-left',
      -1 * this.marginToHide + 'px',
      'important'
    );
    // Browser needs some time so that it won't transition previous setProperty
    setTimeout(() => {
      this.sliderElement.classList.add('tg_tpf_slider_animation');
      this.transitionEvent = this.whichTransitionEvent(this.sliderElement);
      this.sliderElement.addEventListener(
        this.transitionEvent,
        (this.sliderElement.transitionEndHandler = (e) => {
          this.sliderisOpen = this.transitionEndCallback(
            e,
            this,
            true,
            '0px',
            null
          );
          this.isSliderEngaged = false;
          return false;
        })
      );
      this.sliderElement.style.setProperty('margin-left', '0px', 'important');
      jQuery('.tg_dpf_slider_toggle_button').attr('aria-pressed', 'true');
    }, 10);
  },

  closeLeftSlider: function () {
    if (this.sliderElement === undefined || this.isSliderEngaged) {
      return false;
    }
    this.isSliderEngaged = true;
    this.sliderElement.classList.add('tg_tpf_slider_animation');
    this.transitionEvent = this.whichTransitionEvent(this.sliderElement);
    this.sliderElement.addEventListener(
      this.transitionEvent,
      (this.sliderElement.transitionEndHandler = (e) => {
        this.sliderisOpen = this.transitionEndCallback(
          e,
          this,
          false,
          '-5000px',
          null
        );
        this.isSliderEngaged = false;
        return false;
      })
    );
    this.sliderElement.style.setProperty(
      'margin-left',
      -1 * this.marginToHide + 'px',
      'important'
    );
    jQuery('.tg_dpf_slider_toggle_button').attr('aria-pressed', 'false');
  },

  /**
   * slider on the right side
   */
  toggleRightSlider: function () {
    this.getDimensions();
    if (this.sliderMarginRight < -1 * this.sliderWidth) {
      this.openRightSlider();
    } else {
      this.closeRightSlider();
    }
  },

  openRightSlider: function () {
    if (this.sliderElement === undefined || this.isSliderEngaged) {
      return false;
    }
    this.isSliderEngaged = true;
    this.sliderElement.style.setProperty(
      'margin-right',
      -1 * this.sliderWidth + 'px',
      'important'
    );

    // Browser needs some time so that it won't transition previous setProperty
    setTimeout(() => {
      this.sliderElement.classList.add('tg_tpf_slider_animation');
      this.transitionEvent = this.whichTransitionEvent(this.sliderElement);
      this.sliderElement.addEventListener(
        this.transitionEvent,
        (this.sliderElement.transitionEndHandler = (e) => {
          this.sliderisOpen = this.transitionEndCallback(
            e,
            this,
            true,
            null,
            '0px'
          );
          this.isSliderEngaged = false;
          return false;
        })
      );
      this.sliderElement.style.setProperty('margin-right', '0px', 'important');

      jQuery('.tg_dpf_slider_toggle_button').attr('aria-pressed', 'true');
    }, 10);
  },

  closeRightSlider: function () {
    if (this.sliderElement === undefined || this.isSliderEngaged) {
      return false;
    }
    this.isSliderEngaged = true;
    this.sliderElement.classList.add('tg_tpf_slider_animation');
    this.transitionEvent = this.whichTransitionEvent(this.sliderElement);
    this.sliderElement.addEventListener(
      this.transitionEvent,
      (this.sliderElement.transitionEndHandler = (e) => {
        this.sliderisOpen = this.transitionEndCallback(
          e,
          this,
          false,
          null,
          '-5000px'
        );
        this.isSliderEngaged = false;
        return false;
      })
    );
    this.sliderElement.style.setProperty(
      'margin-right',
      -1 * (this.sliderWidth + 80) + 'px',
      'important'
    );
    jQuery('.tg_dpf_slider_toggle_button').attr('aria-pressed', 'false');
  },

  getDimensions: function () {
    if (this.sliderElement.offsetWidth > this.viewPortWidth) {
      this.sliderWidth = this.viewPortWidth - 42; // considering padding and borders
      this.sliderElement.style.setProperty('width', this.sliderWidth + 'px');
    } else {
      this.sliderWidth = this.sliderElement.getBoundingClientRect().width;
    }
    const sliderElementStyle = getComputedStyle(this.sliderElement);
    this.sliderMarginRight = parseInt(sliderElementStyle.marginRight);
    this.sliderMarginLeft = parseInt(sliderElementStyle.marginLeft);
    this.marginToHide = this.sliderElement.offsetWidth;
  },

  sliderLeftListeners: function () {
    // open and close with buttons
    jQuery('.tg_dpf_slider_toggle_button').on('click', () => {
      this.toggleLeftSlider();
      return false;
    });

    // close on click on the background
    jQuery('body').on('click', (e) => {
      this.getDimensions();
      if (
        !this.sliderisOpen ||
        e.pageX < this.sliderMarginLeft + this.sliderWidth
      ) {
        return;
      }
      // ignore elements that close the slider anyway
      for (const classItem of e.target.classList) {
        // ignore the reset button
        if ('tg_dpf_toggle_reset_button' === classItem) {
          return;
        }
        // ignore the jnoty message
        if ('jnoty-message' === classItem) {
          return;
        }
      }
      this.closeLeftSlider();
    });
  },

  sliderRightListeners: function () {
    // open and close with buttons
    jQuery('.tg_dpf_slider_toggle_button').on('click', () => {
      this.toggleRightSlider();
      return false;
    });

    // close on click on the background
    jQuery('body').on('click', (e) => {
      this.getDimensions();
      if (
        !this.sliderisOpen ||
        e.pageX >
          document.documentElement.clientWidth -
            this.sliderMarginRight -
            this.sliderWidth
      ) {
        return;
      }
      // ignore elements that close the slider anyway
      for (const classItem of e.target.classList) {
        // ignore the reset button
        if ('tg_dpf_toggle_reset_button' === classItem) {
          return;
        }
        // ignore the jnoty message
        if ('jnoty-message' === classItem) {
          return;
        }
      }
      this.closeRightSlider();
    });
  },

  whichTransitionEvent: (el) => {
    let transitions = {
      transition: 'transitionend',
      OTransition: 'oTransitionEnd',
      MozTransition: 'transitionend',
      WebkitTransition: 'webkitTransitionEnd',
    };
    for (let t in transitions) {
      if (el.style[t] !== undefined) {
        return transitions[t];
      }
    }
  },

  transitionEndCallback: (
    e,
    _this,
    sliderisOpen,
    finalLeftMargin,
    finalRightMargin
  ) => {
    e.target.classList.remove('tg_tpf_slider_animation');
    e.target.removeEventListener(
      _this.transitionEvent,
      e.target.transitionEndHandler
    );
    if (finalLeftMargin) {
      e.target.style.setProperty('margin-left', finalLeftMargin, 'important');
    }
    if (finalRightMargin) {
      e.target.style.setProperty('margin-right', finalRightMargin, 'important');
    }
    return sliderisOpen;
  },
};
