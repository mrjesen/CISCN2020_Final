<table cellpadding="2" cellspacing="1" width="98%">
    <tr> 
        <td width="150">后台编辑器样式：</td>
        <td><input type="radio" name="setting[toolbar]" value="basic" <?php if ($setting['toolbar'] == 'basic') echo 'checked'; ?>>简洁型 <input type="radio" name="setting[toolbar]" value="full" <?php if ($setting['toolbar'] == 'full') echo 'checked'; ?>> 标准型 </td>
    </tr>
    <tr> 
        <td>前台编辑器样式：</td>
        <td><input type="radio" name="setting[mbtoolbar]" value="basic" <?php if ($setting['mbtoolbar'] == 'basic') echo 'checked'; ?>> 简洁型 <input type="radio" name="setting[mbtoolbar]" value="full" <?php if ($setting['mbtoolbar'] == 'full') echo 'checked'; ?>> 标准型 </td>
    </tr>
    <tr> 
        <td>默认值：</td>
        <td><textarea name="setting[defaultvalue]" rows="2" cols="20" id="defaultvalue" style="height:100px;width:250px;"><?php echo $setting['defaultvalue']; ?></textarea></td>
    </tr>
    <tr> 
        <td>是否保存远程图片：</td>
        <td><input type="radio" name="setting[enablesaveimage]" value="1" <?php if ($setting['enablesaveimage'] == 1) echo 'checked'; ?>> 是 <input type="radio" name="setting[enablesaveimage]" value="0"  <?php if ($setting['enablesaveimage'] == 0) echo 'checked'; ?>> 否</td>
    </tr>
    <tr> 
        <td>编辑器默认高度：</td>
        <td><input type="text" name="setting[height]" value="<?php echo $setting['height']; ?>" size="4" class="input"> px</td>
    </tr>
</table>