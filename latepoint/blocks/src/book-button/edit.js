/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress components that create the necessary UI elements for the block
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-components/
 */
import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';
import {
  TextControl,
  Button,
  SelectControl,
  ToggleControl,
  FontSizePicker,
  ColorIndicator,
  Dropdown,
  DropdownContentWrapper,
  ColorPalette,
  Flex,
  FlexBlock,
  __experimentalGrid as Grid,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption
} from '@wordpress/components';

import {
  __experimentalBoxControl as BoxControl,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
  __experimentalUnitControl as UnitControl,
} from '@wordpress/components';

import {Panel, PanelBody, PanelRow} from '@wordpress/components';
import {useState} from '@wordpress/element';


/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
  useBlockProps,
  InspectorControls,
} from '@wordpress/block-editor';

const LatepointBookButtonWrapper = styled.div`
`;

const LatepointBookButton = styled.div`
`;

const ColorAttributesWrapper = styled.div`
  margin-bottom: 15px;
  border: 1px solid #eee;
`;

const PanelRowBlock = styled(PanelRow)`
  display:block;
  margin-bottom: 20px;
`;

const SingleColumnItem = styled(ToolsPanelItem)`
    grid-column: span 1;
`;

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({attributes, setAttributes}) {
  const blockProps = useBlockProps();

  const colors = [
    {
      "name": "Black",
      "color": "#000000"
    },
    {
      "name": "White",
      "color": "#ffffff"
    },
    {
      "name": "Blue",
      "color": "#5376ea"
    }
  ]

  const renderTextColorPicker = () => (
    <ColorPalette
      value={attributes.text_color}
      colors={colors}
      onChange={(color) => setAttributes({text_color: color})}
    />
  );

  const renderBgColorPicker = () => (
    <ColorPalette
      value={attributes.bg_color}
      colors={colors}
      onChange={(color) => setAttributes({bg_color: color})}
    />
  );

  const generateStyles = () => {
    let styles = {}
    if (attributes.border_radius) styles.borderRadius = attributes.border_radius
    if (attributes.bg_color) styles.backgroundColor = attributes.bg_color
    if (attributes.text_color) styles.color = attributes.text_color
    if (attributes.font_size) styles.fontSize = attributes.font_size
    return styles
  }


  return (
    <div {...blockProps}>
      <InspectorControls>
        <Panel>
          <PanelBody title="Button Settings">
            <TextControl
              label="Caption"
              value={attributes.caption || ''}
              onChange={(value) => setAttributes({caption: value})}
            />
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Booking Form Settings">
            <PanelRow>
              <ToggleControl
                label="Hide Summary Panel"
                checked={attributes.hide_summary}
                onChange={(value) => setAttributes({hide_summary: value})}
              />
            </PanelRow>
            <PanelRow>
              <ToggleControl
                label="Hide Side Panel"
                checked={attributes.hide_side_panel}
                onChange={(value) => setAttributes({hide_side_panel: value})}
              />
            </PanelRow>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Step Settings" initialOpen={false}>
            <PanelRowBlock>
              <SelectControl
                value={attributes.selected_agent}
                label={__('Preselected Agent', 'latepoint')}
                onChange={(value) => setAttributes({selected_agent: value})}
                options={latepoint_helper.selected_agents_options}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <SelectControl
                value={attributes.selected_service}
                label={__('Preselected Service', 'latepoint')}
                onChange={(value) => setAttributes({selected_service: value})}
                options={latepoint_helper.selected_services_options}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <SelectControl
                value={attributes.selected_service_category}
                label={__('Preselected Service Category', 'latepoint')}
                onChange={(value) => setAttributes({selected_service_category: value})}
                options={latepoint_helper.selected_service_categories_options}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <SelectControl
                value={attributes.selected_location}
                label={__('Preselected Location', 'latepoint')}
                onChange={(value) => setAttributes({selected_location: value})}
                options={latepoint_helper.selected_locations_options}
              />
            </PanelRowBlock>
            <PanelRow>
              <TextControl
                label={__('Preselected Booking Start Date', 'latepoint')}
                value={attributes.selected_start_date || ''}
                placeholder="YYYY-MM-DD"
                onChange={(value) => setAttributes({selected_start_date: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={__('Preselected Booking Start Time', 'latepoint')}
                value={attributes.selected_start_time || ''}
                placeholder="Minutes"
                onChange={(value) => setAttributes({selected_start_time: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={__('Preselected Duration', 'latepoint')}
                value={attributes.selected_duration || ''}
                placeholder="Minutes"
                onChange={(value) => setAttributes({selected_duration: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={__('Preselected Total Attendees', 'latepoint')}
                value={attributes.selected_total_attendees || ''}
                placeholder="Number"
                onChange={(value) => setAttributes({selected_total_attendees: value})}
              />
            </PanelRow>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Button Appearance" initialOpen={false}>
            <PanelRow>
              <ColorAttributesWrapper>

                <Dropdown
                  position="bottom left"
                  renderToggle={({isOpen, onToggle}) => (
                    <Button onClick={onToggle} aria-expanded={isOpen}>
                      <Flex>
                        <ColorIndicator colorValue={attributes.bg_color}/>
                        <FlexBlock>{__('Background Color')}</FlexBlock>
                      </Flex>
                    </Button>
                  )}
                  renderContent={renderBgColorPicker}
                />
                <Dropdown
                  position="bottom left"
                  renderToggle={({isOpen, onToggle}) => (
                    <Button onClick={onToggle} aria-expanded={isOpen}>
                      <Flex>
                        <ColorIndicator colorValue={attributes.text_color}/>
                        <FlexBlock>{__('Text Color')}</FlexBlock>
                      </Flex>
                    </Button>
                  )}
                  renderContent={renderTextColorPicker}
                />
              </ColorAttributesWrapper>
            </PanelRow>
            <PanelRowBlock>
              <FontSizePicker
                __nextHasNoMarginBottom
                fontSizes={[
                  {
                    name: __('Small'),
                    slug: 'small',
                    size: '12px',
                  },
                  {
                    name: __('Normal'),
                    slug: 'normal',
                    size: '18px',
                  },
                  {
                    name: __('Big'),
                    slug: 'big',
                    size: '28px',
                  },
                ]}
                value={attributes.font_size}
                fallbackFontSize={attributes.font_size}
                onChange={(value) => setAttributes({font_size: value})}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <UnitControl
                label={__('Border Radius')}
                onChange={(value) => {
                  setAttributes({border_radius: value})
                }}
                units={[
                  {
                    a11yLabel: 'Pixels (px)',
                    label: 'px',
                    step: 1,
                    value: 'px'
                  }
                ]}
                value={attributes.border_radius}
              />
            </PanelRowBlock>
            <PanelRowBlock>
              <ToggleGroupControl
                isBlock
                isDeselectable={true}
                value={attributes.align}
                label={__('Alignment', 'latepoint')}
                onChange={(value) => {
                  setAttributes({align: value})
                }}
              >
                <ToggleGroupControlOption
                  label="Left"
                  value="left"
                />
                <ToggleGroupControlOption
                  label="Center"
                  value="center"
                />
                <ToggleGroupControlOption
                  label="Right"
                  value="right"
                />
                <ToggleGroupControlOption
                  label="Justify"
                  value="justify"
                />
              </ToggleGroupControl>
            </PanelRowBlock>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Other Settings" initialOpen={false}>
            <PanelRow>
              <TextControl
                label="Source ID"
                value={attributes.source_id || ''}
                onChange={(value) => setAttributes({source_id: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Calendar Start Date"
                value={attributes.calendar_start_date || ''}
                placeholder="YYYY-MM-DD"
                onChange={(value) => setAttributes({calendar_start_date: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Show Services"
                placeholder="Comma separated service IDs"
                value={attributes.show_services || ''}
                onChange={(value) => setAttributes({show_services: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Show Service Categories"
                placeholder="Comma separated category IDs"
                value={attributes.show_service_categories || ''}
                onChange={(value) => setAttributes({show_service_categories: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Show Agents"
                placeholder="Comma separated agent IDs"
                value={attributes.show_agents || ''}
                onChange={(value) => setAttributes({show_agents: value})}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Show Locations"
                placeholder="Comma separated location IDs"
                value={attributes.show_locations || ''}
                onChange={(value) => setAttributes({show_locations: value})}
              />
            </PanelRow>
          </PanelBody>
        </Panel>
      </InspectorControls>
      <LatepointBookButtonWrapper
        className={'latepoint-book-button-wrapper ' + (attributes.align ? `latepoint-book-button-align-${attributes.align}` : '')}>
        <LatepointBookButton style={generateStyles()}
                         className="latepoint-book-button">{attributes.caption}</LatepointBookButton>
      </LatepointBookButtonWrapper>
    </div>
  );
}
