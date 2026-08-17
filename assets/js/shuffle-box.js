/**
 *   Last modified: 2021/01/11 18:53:19
 *
 * @package     Tag Groups Pro
 * @author      Christoph Amthor
 * @copyright   2021 Christoph Amthor (@ Chatty Mango, chattymango.com)
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

var TagGroupsShuffleBox = {
  init: function(
    options
  ) {
    this.addPremiumFilter = options.addPremiumFilter || false;
    this.timeoutMilliSecs = options.timeoutMilliSecs || 1000;
    this.divIdInnerTag = '#' + options.divIdInner || '';

    this.timeout = null;
    this.qsPrev = '';
    this.grid = null;
    this.qsText = null;

    jQuery(this.divIdInnerTag + ' .cm-shuffle-box-quicksearch')
      .on('keyup', (event) => {
        this.quickSearch(event.target);
      });

    this.grid = jQuery(this.divIdInnerTag + '_container').isotope({
      layoutMode: options.layoutMode || null,
      filter: (i, itemElem) => {
        return this.filterGrid(itemElem);
      },
    });
    this.setGroup(options.initialGroup || -1);

    jQuery(this.divIdInnerTag + ' .cm-shuffle-box-button').on(
      'click',
      (event) => {
        const id = jQuery(event.target).attr('data-id');
        this.setGroup(id);
      }
    );
  },

  filterGrid: function(itemElem) {
    // search for the text string from quick search
    var qsFilterResult = this.qsText
      ? itemElem.textContent.toLowerCase().indexOf(this.qsText) > -1
      : true;

    // search for the group class name
    var groupFilterResult =
      this.selectedGroup > -1
        ? jQuery(itemElem).hasClass(
            'cm-shuffle-box-group-' + this.selectedGroup
          )
        : true;
    // combine both results with AND
    return qsFilterResult && groupFilterResult;
  },

  setGroup: function(id) {
    // make visible which button is active
    jQuery(this.divIdInnerTag)
      .find('.cm-shuffle-box-button-' + id)
      .addClass('cm-shuffle-box-button-active')
      .attr('aria-selected', 'true');
    jQuery(this.divIdInnerTag)
      .find('.cm-shuffle-box-button:not(.cm-shuffle-box-button-' + id + ')')
      .removeClass('cm-shuffle-box-button-active')
      .attr('aria-selected', 'false');
    this.selectedGroup = id;

    if (this.addPremiumFilter) {
      this.addGroupFilterParams(id);
    }

    this.grid.isotope();
  },

  addGroupFilterParams: function(id) {
    if (id === -1) {
      jQuery(this.divIdInnerTag)
        .find('.cm-shuffle-box-tag-container .tag-groups-tag')
        .each(function () {
          var a = jQuery(this).find('a');
          var href = a.attr('data-href');
          a.attr('href', href);
        });
    } else {
      jQuery(this.divIdInnerTag)
        .find('.cm-shuffle-box-group-' + id)
        .each(function () {
          var a = jQuery(this).find('a');
          var href = a.attr('data-href');
          href +=
            (href.indexOf('?') === -1 ? '?' : '&') +
            'term_group=' +
            id +
            '&term_id=' +
            a.attr('data-termid');
          a.attr('href', href);
        });
    }
  },

  quickSearch: function(target) {
    clearTimeout(this.timeout);
    this.timeout = setTimeout(() => {
      var qs = jQuery(target).val().toLowerCase();
      //prevent bounces
      if (this.qsPrev !== qs) {
        this.qsText = qs;
        this.grid.isotope();
        this.qsPrev = qs;
      }
    }, this.timeoutMilliSecs);
  }
}
