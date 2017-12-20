import React, {Component} from 'react';
import PropTypes from 'prop-types';
import ReactDOM from 'react-dom';
import AddNewField from "./FieldList/Components/AddNewField";

export default class AlwaysNearbyBox extends Component {
  static headerOffsetTop  = 0;
  static viewableAreaSize = 0;
  static pageHeight       = 0;
  static boxMaxHeight     = 0;
  static footerSize       = 0;
  static padding          = 10;

  static domEventsSubscribed = false;

  static propTypes = {
    className: PropTypes.string,
    stickyTop: PropTypes.node,
  };

  parentWidth    = 0;
  parentPaddingX = 15;

  constructor(props, context) {
    super(props, context);

    this.handleScroll           = this.handleScroll.bind(this);
    this.handleWindowResize     = this.handleWindowResize.bind(this);
    this.updateOffsetDimensions = this.updateOffsetDimensions.bind(this);
  }

  componentDidMount() {
    const {wrapper, stickyTop, children} = this.refs;

    wrapper.style.position  = 'fixed';
    wrapper.style.top       = '0px';
    wrapper.style.overflowY = 'auto';
    wrapper.style.width     = '0px';

    children.style.position  = 'relative';
    stickyTop.style.position = 'fixed';
    stickyTop.style.width    = '0px';

    window.addEventListener('scroll', this.handleScroll);
    window.addEventListener('resize', this.handleWindowResize);
    window.addEventListener(AddNewField.EVENT_AFTER_UPDATE, this.handleScroll);

    this.updateOffsetDimensions();
    this.handleScroll();

    setTimeout(this.handleWindowResize, 200);
  }

  componentDidUpdate() {
    this.updateOffsetDimensions();
    this.handleScroll();
  }

  componentWillUnmount() {
    const {wrapper, stickyTop, children} = this.refs;

    wrapper.style.position  = '';
    wrapper.style.top       = '';
    wrapper.style.overflowY = '';
    wrapper.style.width     = '';

    children.style.position  = '';
    stickyTop.style.position = '';
    stickyTop.style.width    = '';

    window.removeEventListener('scroll', this.handleScroll);
    window.removeEventListener('resize', this.handleWindowResize);
    window.removeEventListener(AddNewField.EVENT_AFTER_UPDATE, this.handleScroll);
  }

  updateOffsetDimensions() {
    const body       = document.body,
          html       = document.documentElement,
          builder    = document.getElementById('freeform-builder'),
          parentNode = ReactDOM.findDOMNode(this).parentNode;

    let offset = 0;

    offset += builder.getBoundingClientRect().top;

    setTimeout(function(){
      AlwaysNearbyBox.footerSize = document.getElementsByClassName('footer')[0].parentNode.clientHeight;
    },200);

    AlwaysNearbyBox.headerOffsetTop  = offset;
    AlwaysNearbyBox.viewableAreaSize = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
    AlwaysNearbyBox.pageHeight       = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
    AlwaysNearbyBox.boxMaxHeight     = parentNode.clientHeight;
    this.parentWidth                 = parentNode.clientWidth;
  }

  handleWindowResize() {
    this.updateOffsetDimensions();
    this.handleScroll();
  }

  handleScroll() {
    const {wrapper, stickyTop, children} = this.refs;

    let offsetY = AlwaysNearbyBox.padding,
        height  = AlwaysNearbyBox.viewableAreaSize - (AlwaysNearbyBox.padding * 2);

    const offsetFromHeader = window.scrollY - AlwaysNearbyBox.headerOffsetTop,
          offsetFromFooter = AlwaysNearbyBox.pageHeight - (window.scrollY + AlwaysNearbyBox.viewableAreaSize),
          doublePaddingY   = AlwaysNearbyBox.padding * 2;

    if (offsetFromHeader < 0) {
      offsetY = Math.abs(offsetFromHeader) + AlwaysNearbyBox.padding;
      height -= offsetY;
    }

    if (offsetFromFooter < AlwaysNearbyBox.footerSize) {
      height -= AlwaysNearbyBox.footerSize - offsetFromFooter;
    }

    if (height > AlwaysNearbyBox.boxMaxHeight - doublePaddingY) {
      height = AlwaysNearbyBox.boxMaxHeight - doublePaddingY;
    }

    if (stickyTop) {
      stickyTop.style.width = (this.parentWidth - (this.parentPaddingX * 2)) + 'px';

      children.style.top = stickyTop.clientHeight + 'px';
    }

    wrapper.style.top    = offsetY + 'px';
    wrapper.style.height = height + 'px';
    wrapper.style.width  = (this.parentWidth - (this.parentPaddingX * 2)) + 'px';
  }

  render() {
    return (
      <div className={this.props.className} ref='wrapper'>
        <div ref="stickyTop" className="sticky">
          {this.props.stickyTop}
        </div>
        <div ref="children">
          {this.props.children}
        </div>
      </div>
    );
  }
}
