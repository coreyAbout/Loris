<div class="row">
<div class="col-sm-9">
<div class="panel panel-primary">
    <div class="panel-heading" onclick="hideFilter(this)">
        Selection Filter 
        <span class="glyphicon arrow glyphicon-chevron-up pull-right"></span>
    </div>
    <div class="panel-body">
        <form method="post" action="/acknowledgements/">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label class="col-sm-12 col-md-4">
                        {$form.first_name.label}
                    </label>
                    <div class="col-sm-12 col-md-8">
                        {$form.first_name.html}
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label class="col-sm-12 col-md-4">
                        {$form.last_name.label}
                    </label>
                    <div class="col-sm-12 col-md-8">
                        {$form.last_name.html}
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label class="col-sm-12 col-md-4">
                        {$form.present.label}
                    </label>
                    <div class="col-sm-12 col-md-8">
                        {$form.present.html}
                    </div>
                </div>
            </div>
            <br class="visible-xs">
            <div id="advanced-buttons">
                            <div class="col-sm-4 col-md-3 col-xs-12 col-md-offset-6">
                                <input type="submit" name="filter" value="Show Data" id="showdata_advanced_options" class="btn btn-sm btn-primary col-xs-12" />
                            </div>

                            <div class="visible-xs col-xs-12"> </div>
                            <div class="visible-xs col-xs-12"> </div>
                            <div class="visible-xs col-xs-12"> </div>
                            <div class="visible-xs col-xs-12"> </div>
                            <div class="col-sm-4 col-md-3 col-xs-12">
                                <input type="button" name="reset" value="Clear Form" class="btn btn-sm btn-primary col-xs-12" onclick="location.href='{$baseurl}/acknowledgements/?reset=true'" />
                            </div>
                            <div class="visible-xs col-xs-12"> </div>
                            <div class="visible-xs col-xs-12"> </div>
                            <div class="visible-xs col-xs-12"> </div>
                            <div class="visible-xs col-xs-12"> </div>
            </div>
        </form>
    </div>
</div>
</div>

<div id="tabs" style="background: white">
    <div class="tab-content">
        <div class="tab-pane active">
            <table class="table table-hover table-primary table-bordered table-unresolved-conflicts dynamictable" border="0">
                <thead>
                    <tr class="info">
                        <th>Citation Policy</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td nowrap="nowrap">
                            <div class="col-sm-12 col-md-12">{$citation_policy}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!--  title table with pagination -->
            <table id="LogEntries" border="0" valign="bottom" width="100%">
                <tr>
                    <!-- display pagination links -->
                    <td align="right">{$page_links}</td>
                </tr>
            </table>
                <form method="post" action="/acknowledgements/" name="acknowledgements" id="acknowledgements">
                    <table class="table table-hover table-primary table-bordered table-acknowledgements dynamictable" border="0">
                        <thead>

                            {foreach from=$form.errors item=error}
                            <tr>
                                <td nowrap="nowrap" colspan="6" class="error">{$error}</td>
                            </tr>
                            {/foreach}
                            
                            <tr class="info">
                                    {section name=header loop=$headers}
                                        <th><a href="{$baseurl}/acknowledgements/?filter[order][field]={$headers[header].name}&filter[order][fieldOrder]={$headers[header].fieldOrder}">
                                            {$headers[header].displayName}
                                        </a></th>
                                    {/section}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="width:100px">{$form.addfirst_name.html}</div>
                                </td>
                                <td>
                                    <div style="width:55px">{$form.addinitials.html}</div>
                                </td>
                                <td>
                                    <div style="width:100px">{$form.addlast_name.html}</div>
                                </td>
                                <td>
                                    <div>
                                        <select name="addaffiliations[]" multiple>
                                            <option value="STOP-AD CENTRE, Centre for Studies on Prevention of Alzheimer's disease, Montreal, Qc, Canada">STOP-AD CENTRE, Centre for Studies on Prevention of Alzheimer's disease, Montreal, Qc, Canada</option>
                                            <option value="Douglas Mental Health University Institute Research Centre, affiliated with McGill University, Montreal, Qc, Canada">Douglas Mental Health University Institute Research Centre, affiliated with McGill University, Montreal, Qc, Canada</option>
                                            <option value="McGill University, Montreal, Qc, Canada">McGill University, Montreal, Qc, Canada</option>
                                            <option value="Montreal Neurological Institute and hospital, Montreal, Qc, Canada">Montreal Neurological Institute and hospital, Montreal, Qc, Canada</option>
                                            <option value="McGill University Research Centre for Studies in Aging, Montreal, Qc, Canada">McGill University Research Centre for Studies in Aging, Montreal, Qc, Canada</option>
                                            <option value="Centre de recherche de l'Institut Universitaire de Gériatrie de Montreal, Qc, Canada">Centre de recherche de l'Institut Universitaire de Gériatrie de Montreal, Qc, Canada</option>
                                            <option value="Université de Montréal, Montreal, Qc, Canada">Université de Montréal, Montreal, Qc, Canada</option>
                                            <option value="University of Southern California's Alzheimer's Therapeutic Research Institute, San Diego, CA, USA">University of Southern California's Alzheimer's Therapeutic Research Institute, San Diego, CA, USA</option>
                                            <option value="Massachussets Alzheimer's Disease Research Center, Harvard Medical School, Boston, MA, USA">Massachussets Alzheimer's Disease Research Center, Harvard Medical School, Boston, MA, USA</option>
                                            <option value="John Hopkins University, Baltimore, MD, USA">John Hopkins University, Baltimore, MD, USA</option>
                                            <option value="Khachaturian & Associates Inc, Potomac, MD, USA">Khachaturian & Associates Inc, Potomac, MD, USA</option>
                                            <option value="Mayo Clinic, Rochester, MN, USA">Mayo Clinic, Rochester, MN, USA</option>
                                            <option value="Center for Alzheimer's Research and Treatment Harvard Medical School, Boston, MA, USA">Center for Alzheimer's Research and Treatment Harvard Medical School, Boston, MA, USA</option>
                                            <option value="Wake Forest School of medicine, Winston-Salem, NC, USA">Wake Forest School of medicine, Winston-Salem, NC, USA</option>
                                            <option value="Washington University, Seattle, WA, USA">Washington University, Seattle, WA, USA</option>
                                            <option value="Washington University School of Medecine in St-Louis, MO, USA">Washington University School of Medecine in St-Louis, MO, USA</option>
                                            <option value="Wisconsin Alzheimer's Institute, UW School of Medicine and Public Health, Milwaukee, WI, USA">Wisconsin Alzheimer's Institute, UW School of Medicine and Public Health, Milwaukee, WI, USA</option>
                                            <option value="Banner Alzheimer Institute, Phoenix, AZ, USA">Banner Alzheimer Institute, Phoenix, AZ, USA</option>
                                            <option value="University California, San Diego, School of medicine, La Jolla, CA, USA">University California, San Diego, School of medicine, La Jolla, CA, USA</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <select name="adddegrees[]" multiple>
                                            <option value="BSc">BSc</option>
                                            <option value="BA">BA</option>
                                            <option value="MSc">MSc</option>
                                            <option value="MA">MA</option>
                                            <option value="MMSc">MMSc</option>
                                            <option value="MEng">MEng</option>
                                            <option value="MPH">MPH</option>
                                            <option value="PharmD">PharmD</option>
                                            <option value="PhD">PhD</option>
                                            <option value="MD">MD</option>
                                            <option value="CM">CM</option>
                                            <option value="LPN">LPN</option>
                                            <option value="RN">RN</option>
                                            <option value="MIT">MIT</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <select name="addroles[]" multiple>
                                            <option value="Investigators">Investigators</option>
                                            <option value="Project Administration">Project Administration</option>
                                            <option value="Database Management">Database Management</option>
                                            <option value="Interview Data Collection">Interview Data Collection</option>
                                            <option value="Data Analyses">Data Analyses</option>
                                            <option value="Image Acquisition (MRI/PET/MEG)">Image Acquisition (MRI/PET/MEG)</option>
                                            <option value="Data Entry">Data Entry</option>
                                            <option value="Clinical Evaluation">Clinical Evaluation</option>
                                            <option value="Cognitive Evaluation">Cognitive Evaluation</option>
                                            <option value="Database Programming">Database Programming</option>
                                            <option value="Imaging Processing and Evaluation (MRI/PET/MEG)">Imaging Processing and Evaluation (MRI/PET/MEG)</option>
                                            <option value="Genetic Analysis and Biochemical Assays">Genetic Analysis and Biochemical Assays</option>
                                            <option value="Randomization and Pharmacy Allocation">Randomization and Pharmacy Allocation</option>
                                            <option value="Consultants">Consultants</option>
                                            <option value="LP/CSF collection">LP/CSF collection</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div>{$form.addstart_date.html}</div>
                                </td>
                                <td>
                                    <div>{$form.addend_date.html}</div>
                                </td>
                                <td>
                                    <div>{$form.addpresent.html}</div>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap="nowrap" colspan="8" id="message-area">
                                    
                                </td>
                                <td nowrap="nowrap">
                                    <input class="btn btn-sm btn-primary" name="fire_away" value="Save" type="submit" />
                                    <input class="btn btn-sm btn-primary" value="Reset" type="reset" />
                                </td>
                            </tr>
                            {section name=item loop=$items}
                            <tr>
                                {section name=piece loop=$items[item]}
                                    {if $items[item][piece].name != ""}
                                        <td>
                                            {if $items[item][piece].value == "bachelors"}
                                                Bachelors
                                            {elseif $items[item][piece].value == "masters"}
                                                Masters
                                            {elseif $items[item][piece].value == "phd"}
                                                PhD
                                            {elseif $items[item][piece].value == "postdoc"}
                                                Postdoctoral
                                            {elseif $items[item][piece].value == "md"}
                                                MD
                                            {elseif $items[item][piece].value == "registered_nurse"}
                                                Registered Nurse
                                            {else}
                                                {$items[item][piece].value}
                                            {/if}
                                        </td>
                                    {/if}
                                {/section}
                            </tr>
                            {sectionelse}
                                <tr>
                                    <tr><td colspan="10">You're not alone.</td></tr>
                                </tr>
                            {/section}
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
